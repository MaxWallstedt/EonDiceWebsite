<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "error" => "");

function roll_dice(&$rollDetails, $nDice, $nSides, $nPlus) {
	$result = 0;
	$roll;

	$rollDetails .= "{";

	for ($i = 0; $i < intval($nDice); ++$i) {
		$roll = my_rand(1, intval($nSides));

		if (intval($nSides) == 6 && $roll == 6) {
			$result += roll_dice($rollDetails, 2, 6, 0);

			if ($i < intval($nDice) - 1) {
				$rollDetails .= ", ";
			}
		} else {
			$rollDetails .= "$roll";

			if ($i < intval($nDice) - 1) {
				$rollDetails .= ", ";
			}

			$result += $roll;
		}
	}

	$rollDetails .= "}";

	return $result + intval($nPlus);
}

if (!array_key_exists("gameid", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'gameid' parameter";
	die(json_encode($res));
}

if (!array_key_exists("passcode", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'passcode' parameter";
	die(json_encode($res));
}

if (!array_key_exists("characterid", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'characterid' parameter";
	die(json_encode($res));
}

if (!array_key_exists("charactersecret", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'charactersecret' parameter";
	die(json_encode($res));
}

if (!array_key_exists("ndice", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'ndice' parameter";
	die(json_encode($res));
}

if (!array_key_exists("nsides", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'nsides' parameter";
	die(json_encode($res));
}

if (!array_key_exists("nplus", $_POST)) {
	$res["ok"] = "false";
	$res["error"] = "Missing 'nplus' parameter";
	die(json_encode($res));
}

$gameID = $_POST["gameid"];
$gamePasscode = $_POST["passcode"];
$characterID = $_POST["characterid"];
$characterSecret = $_POST["charactersecret"];
$nDice = $_POST["ndice"];
$nSides = $_POST["nsides"];
$nPlus = $_POST["nplus"];

database_connect($res) or die(json_encode($res));
database_valid_game_id($res, $gameID) or die(json_encode($res));
database_valid_game_passcode($res, $gameID, $gamePasscode) or die(json_encode($res));
database_valid_character_id($res, $gameID, $characterID) or die(json_encode($res));
database_valid_character_secret($res, $characterID, $characterSecret) or die(json_encode($res));

$rollString = "$nDice" . "T$nSides+$nPlus";
$rollResult = "";
$rollDetails = "";

$rollResult = "" . roll_dice($rollDetails, $nDice, $nSides, $nPlus);

database_add_roll($res, $gameID, $characterID, $rollString, $rollResult, $rollDetails) or die(json_encode($res));

echo json_encode($res);
?>
