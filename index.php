<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8">
  <title>EonDice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<script defer>
setTimeout(getGames, 5000);

function getGames() {
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var res = JSON.parse(xmlhttp.responseText);
			var gamesList = document.getElementById("games-list");

			if (res.ok == "false") {
				gamesList.innerHTML = "<li>" + res.error + "</li>\n";
			} else if (res.games.length == 0) {
				gamesList.innerHTML = "<li>Det finns inga spel än</li>\n";
			} else {
				gamesList.innerHTML = "";

				for (var i = 0; i < res.games.length; ++i) {
					gamesList.innerHTML += "<li>" +
					                        "<a href=\"enter-game.php?gameid=" + res.games[i].id + "\">" +
					                          res.games[i].name +
					                        "</a>" +
					                       "</li>\n";
				}
			}

			setTimeout(getGames, 5000);
		}
	};

	xmlhttp.open("GET", "get-games-json.php", true);
	xmlhttp.send();
}
</script>
 </head>
 <body>
  <div id="wrapper">
   <h2>EonDice</h2>
   <a href="new-game.php"><p>Skapa spel som spelledare</p></a>
   <h3>Spel</h3>
   <ul id="games-list">
<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "games" => array(), "error" => "");

if (!database_connect($res)) {
	echo "<li>" . $res["error"] . "</li>\n";
} else if (!database_get_games($res)) {
	echo "<li>" . $res["error"] . "</li>\n";
} else if (count($res["games"]) === 0) {
	echo "<li>Det finns inga spel än</li>\n";
} else {
	foreach ($res["games"] as $game) {
		echo "<li><a href=\"enter-game.php?gameid=" . $game["id"] . "\">" . $game["name"] . "</a></li>\n";
	}
}
?>
   </ul>
  </div>
 </body>
</html>
