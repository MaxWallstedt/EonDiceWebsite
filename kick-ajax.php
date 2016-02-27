<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "error" => "");

if (!array_key_exists("gameid", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'gameid' parameter";
	die(json_encode($res));
}

if (!array_key_exists("gamesecret", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'gamesecret' parameter";
	die(json_encode($res));
}

if (!array_key_exists("kickid", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'kickid' parameter";
	die(json_encode($res));
}

$gameID = $_POST["gameid"];
$gameSecret = $_POST["gamesecret"];
$kickID = $_POST["kickid"];

database_connect($res) or die(json_encode($res));
database_valid_game_id($res, $gameID) or die(json_encode($res));
database_valid_game_secret($res, $gameID, $gameSecret) or die(json_encode($res));
database_kick($res, $kickID) or die(json_encode($res));

echo json_encode($res);
?>
