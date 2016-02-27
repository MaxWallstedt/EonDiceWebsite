<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "gameID" => "", "gameSecret" => "", "characterID" => "", "characterSecret" => "", "error" => "");

$gameName = $_POST["gamename"];
$passcode = $_POST["passcode"];

database_connect($res) or die(json_encode($res));

if (!database_create_game($res, $gameName, $passcode)) {
	if ($res["error"] === "Invalid passcode") {
		header("Location: new-game.php?invalid=passcode");
		die();
	} else {
		die(json_encode($res));
	}
}

database_available_character_name($res, $res["gameID"], "spelledare") or die(json_encode($res));
database_create_character($res, $res["gameID"], "spelledare") or die(json_encode($res));

header("Location: game.php?gameid=" . $res["gameID"] . "&passcode=$passcode&gamesecret=" . $res["gameSecret"] . "&characterid=" . $res["characterID"] . "&charactersecret=" . $res["characterSecret"]);
?>
