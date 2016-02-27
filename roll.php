<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

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

array_key_exists("gameid", $_POST) or die("<p>Missing 'gameid' parameter</p>");
array_key_exists("passcode", $_POST) or die("<p>Missing 'passcode' parameter</p>");
array_key_exists("characterid", $_POST) or die("<p>Missing 'characterid' parameter</p>");
array_key_exists("charactersecret", $_POST) or die("<p>Missing 'charactersecret' parameter</p>");
array_key_exists("ndice", $_POST) or die("<p>Missing 'ndice' parameter</p>");
array_key_exists("nsides", $_POST) or die("<p>Missing 'nsides' parameter</p>");
array_key_exists("nplus", $_POST) or die("<p>Missing 'nplus' parameter</p>");

$gameID = $_POST["gameid"];
$gamePasscode = $_POST["passcode"];
$characterID = $_POST["characterid"];
$characterSecret = $_POST["charactersecret"];
$nDice = $_POST["ndice"];
$nSides = $_POST["nsides"];
$nPlus = $_POST["nplus"];

$res = array("ok" => "true", "error" => "");

database_connect($res) or die("<p>" . $res["error"] . "</p>");
database_valid_game_id($res, $gameID) or die("<p>" . $res["error"] . "</p>");
database_valid_game_passcode($res, $gameID, $gamePasscode) or die("<p>" . $res["error"] . "</p>");
database_valid_character_id($res, $gameID, $characterID) or die("<p>" . $res["error"] . "</p>");
database_valid_character_secret($res, $characterID, $characterSecret) or die("<p>" . $res["error"] . "</p>");

$rollString = "$nDice" . "T$nSides+$nPlus";
$rollResult = "";
$rollDetails = "";

$rollResult = "" . roll_dice($rollDetails, $nDice, $nSides, $nPlus);

database_add_roll($res, $gameID, $characterID, $rollString, $rollResult, $rollDetails) or die("<p>" . $res["error"] . "</p>");

$url = "game.php?gameid=$gameID";

foreach ($_POST as $key => $val) {
	if ($key === "gameid" || $key === "ndice" || $key === "nsides" || $key === "nplus") {
		continue;
	}

	$url .= "&$key=$val";
}

header("Location: $url");
?>
