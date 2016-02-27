<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "characters" => array(), "rolls" => array(), "isMaster" => "FALSE", "error" => "");

if (!array_key_exists("gameid", $_GET)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'gameid' parameter";
	die(json_encode($res));
}

if (!array_key_exists("passcode", $_GET)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'passcode' parameter";
	die(json_encode($res));
}

if (!array_key_exists("characterid", $_GET)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'characterid' parameter";
	die(json_encode($res));
}

if (!array_key_exists("charactersecret", $_GET)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'charactersecret' parameter";
	die(json_encode($res));
}

$gameID = $_GET["gameid"];
$passcode = $_GET["passcode"];
$characterID = $_GET["characterid"];
$characterSecret = $_GET["charactersecret"];

database_connect($res) or die(json_encode($res));
database_valid_game_id($res, $gameID) or die(json_encode($res));
database_valid_game_passcode($res, $gameID, $passcode) or die(json_encode($res));
database_valid_character_id($res, $gameID, $characterID) or die(json_encode($res));
database_valid_character_secret($res, $characterID, $characterSecret) or die(json_encode($res));

database_get_game_characters($res, $gameID) or die(json_encode($res));
database_get_game_rolls($res, $gameID) or die(json_encode($res));

$res["isMaster"] = $isMaster;

echo json_encode($res);
?>
