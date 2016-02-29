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
	document.getElementById('create-game-form').onsubmit = submit;
}

function submit() {
	var gameName = document.getElementById("game-name").value;
	var passcode = document.getElementById("passcode").value;
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);

			if (res.ok == "false") {
				document.getElementById("error-paragraph").style.display = "block";
				document.getElementById("error-msg").innerHTML = res.error;
			} else {
				window.location = "game.php?gameid=" + res.gameID + "&passcode=" + passcode + "&gamesecret=" + res.gameSecret + "&characterid=" + res.characterID + "&charactersecret=" + res.characterSecret;
			}
		}
	};

	xmlhttp.open("POST", "create-game-ajax.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("gamename=" + gameName + "&passcode=" + passcode);
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

$invalid = "";

if (array_key_exists("invalid", $_GET)) {
	$invalid = $_GET["invalid"];
}

if ($invalid === "passcode") {
	echo "<p id=\"error-paragraph\"><i id=\"error-msg\">Ogiltig nyckel angiven</i></p>\n";
} else {
	echo "<p id=\"error-paragraph\" style=\"display: none;\"><i id=\"error-msg\"></i></p>\n";
}
?>
   <h3>Nytt spel</h3>
   <form id="create-game-form" method="POST" action="create-game.php">
    <div><label for="game-name">Spelnamn</label></div>
    <div><input id="game-name" type="text" name="gamename" autocomplete="off" required></div>
    <div><label for="passcode">Hemlig nyckel (OBS krypteras ej)</label></div>
    <div><input id="passcode" type="text" name="passcode" autocomplete="off" required></div>
    <div><input type="submit" value="Skapa Spel"></div>
   </form>
  </div>
 </body>
</html>
