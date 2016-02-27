<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8">
  <title>EonDice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
p {
	margin: 0.25em;
}

#destroy-form {
	margin-bottom: 0.5em;
}

#characters-box, #rolls-box {
	width: 17em;
	height: 8em;
	margin: 1em 0;
	background-color: white;
	border-radius: 5px;
	box-shadow: 1px 1px 5px #888888;
	overflow-y: scroll;
	resize: both;
}

#characters-box > div {
	display: flex;
	flex-direction: row;
	align-items: center;
}

#characters-box > div > div:nth-child(2) {
	margin-left: 0.25em;
}

#roll-row {
	display: flex;
	flex-direction: row;
	align-items: center;
}
</style>
<script defer>
setTimeout(getUpdates, 5000);

window.onload = function() {
	var destroyForm = document.getElementById("destroy-form");
	var leaveForm = document.getElementById("leave-form");
	var rollForm = document.getElementById("roll-form");

	if (destroyForm != null) {
		destroyForm.onsubmit = destroyGame;
		updateKickAjax();
	}

	leaveForm.onsubmit = leaveGame;
	rollForm.onsubmit = roll;
};

function roll() {
	var gameID = document.getElementById("roll-gameid").value;
	var passcode = document.getElementById("roll-passcode").value;
	var characterID = document.getElementById("roll-characterid").value;
	var characterSecret = document.getElementById("roll-charactersecret").value;
	var nDice = document.getElementById("ndice-inp").value;
	var nSides = document.getElementById("nsides-inp").value;
	var nPlus = document.getElementById("nplus-inp").value;

	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);

			if (res.ok == "false") {
				alert(res.error);
			}
		}
	};

	xmlhttp.open("POST", "roll-ajax.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("gameid=" + gameID + "&passcode=" + passcode + "&characterid=" + characterID + "&charactersecret=" + characterSecret + "&ndice=" + nDice + "&nsides=" + nSides + "&nplus=" + nPlus);
	return false;
}

function updateKickAjax() {
	var kickForms = document.getElementsByClassName("kick-form");

	for (var i = 0; i < kickForms.length; ++i) {
		var kickID = kickForms[i].elements["kickid"].value;
		kickForms[i].onsubmit = function () {
			var xmlhttp = new XMLHttpRequest();
			var gameID = document.getElementById("destroy-game-id").value;
			var gameSecret = document.getElementById("destroy-game-secret").value;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var res = JSON.parse(xmlhttp.responseText);

					if (res.ok == "false") {
						alert(res.error);
					}
				}
			};

			xmlhttp.open("POST", "kick-ajax.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send("gameid=" + gameID + "&gamesecret=" + gameSecret + "&kickid=" + kickID);
			return false;
		};
	}
}

function destroyGame() {
	var xmlhttp = new XMLHttpRequest();
	var gameID = document.getElementById("destroy-game-id").value;
	var gameSecret = document.getElementById("destroy-game-secret").value;

	if (!confirm("Är du säker? All data om spelet, inklusive alla karaktärer, kommer att raderas.")) {
		return false;
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);

			if (res.ok == "false") {
				alert(res.error);
			} else {
				window.location = '.';
			}
		}
	};

	xmlhttp.open("POST", "destroy-game-ajax.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("gameid=" + gameID + "&gamesecret=" + gameSecret);
	return false;
}

function leaveGame() {
	var xmlhttp = new XMLHttpRequest();
	var characterID = document.getElementById("leave-character-id").value;
	var characterSecret = document.getElementById("leave-character-secret").value;

	if (document.getElementById("destroy-game-secret") != null) {
		if (!confirm("Är du säker? All data din karaktär kommer att raderas, och någon annan kommer kunna logga in med namnet 'spelledare' och agera spelledare.")) {
			return false;
		}
	} else {
		if (!confirm("Är du säker? All data din karaktär kommer att raderas.")) {
			return false;
		}
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);

			if (res.ok == "false") {
				alert(res.error);
			} else {
				window.location = '.';
			}
		}
	};

	xmlhttp.open("POST", "leave-game-ajax.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("characterid=" + characterID + "&charactersecret=" + characterSecret);
	return false;
}

function getUpdates() {
	var xmlhttp = new XMLHttpRequest();
	var gameID = document.getElementById("roll-gameid").value;
	var gameSecret = "";

	if (document.getElementById("destroy-game-secret") != null) {
		gameSecret = document.getElementById("destroy-game-secret").value;
	}

	var passcode = document.getElementById("roll-passcode").value;
	var characterID = document.getElementById("leave-character-id").value;
	var characterSecret = document.getElementById("leave-character-secret").value;

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);

			if (res.ok == "false") {
				alert(res.error);
				window.location = '.';
			} else {
				var charactersBox = document.getElementById("characters-box");
				var rollsBox = document.getElementById("rolls-box");
				var charactersStr = "";
				var rollsStr = "";

				for (var i = 0; i < res.characters.length; ++i) {
					charactersStr += "<div><div><p>";

					if (res.characters[i].isMaster == "t") {
						charactersStr += "<i>";
					}

					charactersStr += res.characters[i].name;

					if (res.characters[i].isMaster == "t") {
						charactersStr += "</i>";
					}

					charactersStr += "</p></div>";

					if (res.isMaster == "TRUE" && res.characters[i].isMaster != "t") {
						charactersStr += "<div>";
						charactersStr += "<form class=\"kick-form\" method=\"POST\" action=\"kick.php\">";
						charactersStr += "<input type=\"hidden\" name=\"gameid\" value=\"" + gameID + "\">";
						charactersStr += "<input type=\"hidden\" name=\"gamesecret\" value=\"" + gameSecret + "\">";
						charactersStr += "<input type=\"hidden\" name=\"kickid\" value=\"" + res.characters[i].id + "\">";
						charactersStr += "<input type=\"submit\" value=\"Kick\">";
						charactersStr += "</form>";
						charactersStr += "</div>";
					}

					charactersStr += "</div>\n";
				}

				charactersBox.innerHTML = charactersStr;

				if (res.isMaster == "TRUE") {
					updateKickAjax();
				}

				for (var i = 0; i < res.rolls.length; ++i) {
					rollsStr += "<div>";
					rollsStr += "<p>";

					if (res.rolls[i].isMaster == "t") {
						rollsStr += "<i>";
					}

					rollsStr += res.rolls[i].name + " - " + res.rolls[i].roll + ": <b>" + res.rolls[i].result + "</b> " + res.rolls[i].details;

					if (res.rolls[i].isMaster == "t") {
						rollsStr += "</i>";
					}

					rollsStr += "</p>";
					rollsStr += "</div>\n";
				}

				rollsBox.innerHTML = rollsStr;

				setTimeout(getUpdates, 5000);
			}
		}
	};

	xmlhttp.open("GET", "get-updates-json.php?gameid=" + gameID + "&passcode=" + passcode + "&characterid=" + characterID + "&charactersecret=" + characterSecret, true);
	xmlhttp.send();
}
</script>
 </head>
 <body>
  <div id="wrapper">
   <h2>EonDice</h2>
<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

array_key_exists("gameid", $_GET) or die("<p>Missing 'gameid' parameter</p>");
array_key_exists("passcode", $_GET) or die("<p>Missing 'passcode' parameter</p>");
array_key_exists("characterid", $_GET) or die("<p>Missing 'characterid' parameter</p>");
array_key_exists("charactersecret", $_GET) or die("<p>Missing 'charactersecret' parameter</p>");

$gameID = $_GET["gameid"];
$passcode = $_GET["passcode"];
$characterID = $_GET["characterid"];
$characterSecret = $_GET["charactersecret"];

$res = array("ok" => "true", "gameName" => "", "characters" => array(), "rolls" => array(), "error" => "");

database_connect($res) or die("<p>" . $res["error"] . "</p>");
database_valid_game_id($res, $gameID) or die("<p>" . $res["error"] . "</p>");
database_valid_game_passcode($res, $gameID, $passcode) or die("<p>" . $res["error"] . "</p>");
database_get_game_name($res, $gameID) or die("<p>" . $res["error"] . "</p>");

echo "<h3>" . $res["gameName"] . "</h3>\n";

database_valid_character_id($res, $gameID, $characterID) or die("<p>" . $res["error"] . "</p>");
database_valid_character_secret($res, $characterID, $characterSecret) or die("<p>" . $res["error"] . "</p>");

if (array_key_exists("gamesecret", $_GET)) {
	$gameSecret = $_GET["gamesecret"];
	database_valid_game_secret($res, $gameID, $gameSecret) or die("<p>" . $res["error"] . "</p>");
	echo "<form id=\"destroy-form\" method=\"POST\" action=\"destroy-game.php\">\n";
	echo "<input id=\"destroy-game-id\" type=\"hidden\" name=\"gameid\" value=\"$gameID\">\n";
	echo "<input id=\"destroy-game-secret\" type=\"hidden\" name=\"gamesecret\" value=\"$gameSecret\">\n";
	echo "<input type=\"submit\" value=\"Avsluta spel\">\n";
	echo "</form>\n";
}

echo "<form id=\"leave-form\" method=\"POST\" action=\"leave-game.php\">\n";
echo "<input id=\"leave-character-id\" type=\"hidden\" name=\"characterid\" value=\"$characterID\">\n";
echo "<input id=\"leave-character-secret\" type=\"hidden\" name=\"charactersecret\" value=\"$characterSecret\">\n";
echo "<input type=\"submit\" value=\"Lämna spel\">\n";
echo "</form>\n";

database_get_game_characters($res, $gameID) or die("<p>" . $res["error"] . "</p>");

echo "<h3>Karaktärer</h3>\n";
echo "<div id=\"characters-box\">\n";

foreach ($res["characters"] as $character) {
	echo "<div>\n";
	echo "<div><p>\n";

	if ($character["isMaster"] === "t") {
		echo "<i>";
	}

	echo $character["name"];

	if ($character["isMaster"] === "t") {
		echo "</i>";
	}
	
	echo "</p></div>\n";

	if ($isMaster === "TRUE" && $character["isMaster"] !== "t") {
		echo "<div>\n";
		echo "<form class=\"kick-form\" method=\"POST\" action=\"kick.php\">\n";

		foreach ($_GET as $key => $val) {
			echo "<input type=\"hidden\" name=\"$key\" value=\"$val\">\n";
		}

		echo "<input type=\"hidden\" name=\"kickid\" value=\"" . $character["id"] . "\">\n";
		echo "<input type=\"submit\" value=\"Kick\">\n";
		echo "</form>\n";
		echo "</div>\n";
	}

	echo "</div>\n";
}

echo "</div>\n";

database_get_game_rolls($res, $gameID) or die("<p>" . $res["error"] . "</p>");

echo "<h3>Tärningskast</h3>\n";
echo "<div id=\"rolls-box\">\n";

foreach ($res["rolls"] as $roll) {
	echo "<div>\n";
	echo "<p>\n";

	if ($roll["isMaster"] === "t") {
		echo "<i>\n";
	}

	echo $roll["name"] . " - " . $roll["roll"] . ": <b>" . $roll["result"] . "</b> " . $roll["details"] . "\n";

	if ($roll["isMaster"] === "t") {
		echo "</i>\n";
	}

	echo "</p>\n";
	echo "</div>\n";
}

echo "</div>\n";

echo "<form id=\"roll-form\" method=\"POST\" action=\"roll.php\">\n";

foreach ($_GET as $key => $val) {
	echo "<input id=\"roll-$key\" type=\"hidden\" name=\"$key\" value=\"$val\">\n";
}

echo "<div id=\"roll-row\">\n";
echo  "<div>\n";
echo   "<input id=\"ndice-inp\" type=\"number\" min=\"0\" step=\"1\" name=\"ndice\" value=\"0\" style=\"width: 3em;\">\n";
echo  "</div>\n";
echo  "<div style=\"margin-left: 0.25em; margin-right: 0.25em;\">\n";
echo   "<p>T</p>\n";
echo  "</div>\n";
echo  "<div>\n";
echo   "<select id=\"nsides-inp\" name=\"nsides\">\n";
echo    "<option value=\"6\">6</option>\n";
echo    "<option value=\"10\">10</option>\n";
echo   "</select>\n";
echo  "</div>\n";
echo  "<div style=\"margin-left: 0.25em; margin-right: 0.25em;\">\n";
echo   "<p>+</p>\n";
echo  "</div>\n";
echo  "<div>\n";
echo   "<input id=\"nplus-inp\" type=\"number\" min=\"0\" step=\"1\" name=\"nplus\" value=\"0\" style=\"width: 3em;\">\n";
echo  "</div>\n";
echo  "<div style=\"margin-left: 0.25em;\">\n";
echo   "<input type=\"submit\" value=\"Slå\">\n";
echo  "</div>\n";
echo "</div>\n";
?>
  </div>
 </body>
</html>
