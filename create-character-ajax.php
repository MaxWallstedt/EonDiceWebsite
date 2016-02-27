<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "gameSecret" => "", "characterID" => "", "characterSecret" => "", "error" => "");

$gameID = $_POST["gameid"];
$characterName = $_POST["charactername"];
$passcode = $_POST["passcode"];

database_connect($res) or die(json_encode($res));
database_valid_game_id($res, $gameID) or die(json_encode($res));
database_valid_game_passcode($res, $gameID, $passcode) or die(json_encode($res));
database_available_character_name($res, $gameID, $characterName) or die(json_encode($res));
database_create_character($res, $gameID, $characterName) or die(json_encode($res));

if ($isMaster === "TRUE") {
	database_get_game_secret($res, $gameID) or die(json_encode($res));
}

echo json_encode($res);
?>
