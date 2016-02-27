<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

array_key_exists("gameid", $_POST) or die("<p>Missing 'gameid' parameter</p>");
array_key_exists("gamesecret", $_POST) or die("<p>Missing 'gamesecret' parameter</p>");
array_key_exists("kickid", $_POST) or die("<p>Missing 'kickid' parameter</p>");

$gameID = $_POST["gameid"];
$gameSecret = $_POST["gamesecret"];
$kickID = $_POST["kickid"];

$res = array("ok" => "true", "error" => "");

database_connect($res) or die("<p>" . $res["error"] . "</p>");
database_valid_game_id($res, $gameID) or die("<p>" . $res["error"] . "</p>");
database_valid_game_secret($res, $gameID, $gameSecret) or die("<p>" . $res["error"] . "</p>");
database_kick($res, $kickID) or die("<p>" . $res["error"] . "</p>");

$url = "game.php?gameid=$gameID";

foreach ($_POST as $key => $val) {
	if ($key === "gameid" || $key === "kickid") {
		continue;
	}

	$url .= "&$key=$val";
}

header("Location: $url");
?>
