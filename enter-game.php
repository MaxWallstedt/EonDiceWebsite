<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8">
  <title>EonDice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="apple-touch-icon-precomposed" href="icons/favicon-152.png">
  <meta name="msapplication-TileColor" content="#FFFFFF">
  <meta name="msapplication-TileImage" content="icons/favicon-144.png">
  <link rel="apple-touch-icon-precomposed" sizes="152x152" href="icons/favicon-152.png">
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="icons/favicon-144.png">
  <link rel="apple-touch-icon-precomposed" sizes="120x120" href="icons/favicon-120.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="icons/favicon-114.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="icons/favicon-72.png">
  <link rel="apple-touch-icon-precomposed" href="icons/favicon-57.png">
  <link rel="icon" href="icons/favicon-32.png" sizes="32x32">
<script defer>
window.onload = init;

function init() {
	document.getElementById('enter-game-form').onsubmit = submit;
}

function submit() {
	var gameID = document.getElementById("game-id").value;
	var characterName = document.getElementById("character-name").value;
	var passcode = document.getElementById("passcode").value;
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);

			if (res.ok == "false") {
				document.getElementById("error-paragraph").style.display = "block";
				document.getElementById("error-msg").innerHTML = res.error;
			} else if (res.gameSecret == "") {
				window.location = "game.php?gameid=" + gameID + "&passcode=" + passcode + "&characterid=" + res.characterID + "&charactersecret=" + res.characterSecret;
			} else {
				window.location = "game.php?gameid=" + gameID + "&passcode=" + passcode + "&gamesecret=" + res.gameSecret + "&characterid=" + res.characterID + "&charactersecret=" + res.characterSecret;
			}
		}
	};

	xmlhttp.open("POST", "create-character-ajax.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("gameid=" + gameID + "&charactername=" + characterName + "&passcode=" + passcode);
	return false;
}
</script>
 </head>
 <body>
  <div id="wrapper">
   <h2>EonDice</h2>
   <a href="."><p>&lt; tillbaka</p></a>
<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$gameID = $_GET["gameid"];
$invalid = "";

$res = array("ok" => "true", "gameName" => "", "error" => "");

if (array_key_exists("invalid", $_GET)) {
	$invalid = $_GET["invalid"];
}

if (!database_connect($res)) {
	echo "<p>" . $res["error"] . "</p>\n";
} elseif (!database_get_game_name($res, $gameID)) {
	echo "<p>" . $res["error"] . "</p>\n";
} else {
	if ($invalid === "charactername") {
		echo "<p id=\"error-paragraph\"><i id=\"error-msg\">Spelarnamnet du angav är upptaget</i></p>\n";
	} elseif ($invalid === "passcode") {
		echo "<p id=\"error-paragraph\"><i id=\"error-msg\">Ogiltig nyckel angiven</i></p>\n";
	} else {
		echo "<p id=\"error-paragraph\" style=\"display: none;\"><i id=\"error-msg\"></i></p>\n";
	}

	echo "<h3>" . $res["gameName"] . "</h3>\n";
	echo "<form id=\"enter-game-form\" method=\"POST\" action=\"create-character.php\">\n";
	echo "<input id=\"game-id\" type=\"hidden\" name=\"gameid\" value=\"$gameID\">\n";
	echo "<div><label for=\"character-name\">Spelarnamn</label></div>\n";
	echo "<div><input id=\"character-name\" type=\"text\" name=\"charactername\" autocomplete=\"off\" required></div>\n";
	echo "<div><label for=\"passcode\">Hemlig nyckel (OBS krypteras ej)</label></div>\n";
	echo "<div><input id=\"passcode\" type=\"text\" name=\"passcode\" autocomplete=\"off\" required></div>\n";
	echo "<div><input type=\"submit\" value=\"Spela\"></div>\n";
	echo "</form>\n";
}
?>
  </div>
 </body>
</html>
