<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

array_key_exists("gameid", $_POST) or die("<p>Missing 'gameid' parameter</p>");
array_key_exists("gamesecret", $_POST) or die("<p>Missing 'gamesecret' parameter</p>");

$gameID = $_POST["gameid"];
$gameSecret = $_POST["gamesecret"];

$res = array("ok" => "true", "error" => "");

database_connect($res) or die("<p>" . $res["error"] . "</p>");
database_destroy_game($res, $gameID, $gameSecret) or die("<p>" . $res["error"] . "</p>");

header("Location: .");
?>
