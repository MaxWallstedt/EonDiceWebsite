<?php
include "my-rand.php";

$db = null;

#########################
# Set a master passcode #
#########################

$masterPasscode = "";
$isMaster = "FALSE";

function database_connect(&$response) {
	global $db;

	##############################
	# Connect to a psql database #
	##############################

	# Set a host name
	$host = "";

	# Set a databas name
	$dbname = "";

	# Set a user name
	$user = "";

	# Set a password
	$password = "";
	$dbString = "host=$host dbname=$dbname user=$user password=$password";

	if (!($db = pg_connect($dbString))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	return true;
}

function database_get_games(&$response) {
	global $db;

	$query = "SELECT GameID, Name FROM Games;";

	$result = null;

	if (!($result = pg_query($db, $query))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	while ($row = pg_fetch_row($result)) {
		array_push($response["games"],
		           array("id"   => $row[0],
		                 "name" => $row[1])
		);
	}

	return true;
}

function database_get_game_characters(&$response, $gameID) {
	global $db;

	$query = "SELECT Name, CharacterID, IsMaster FROM Characters\n";
	$query .= "WHERE GameID = \$1;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	while ($row = pg_fetch_row($result)) {
		array_push($response["characters"], array(
			"name"     => $row[0],
			"id"       => $row[1],
			"isMaster" => $row[2]
		));
	}

	return true;
}

function database_get_game_rolls(&$response, $gameID) {
	global $db;

	$query = "SELECT Name, RollString, RollResult, RollDetails, IsMaster\n";
	$query .= "FROM Rolls NATURAL JOIN Characters\n";
	$query .= "WHERE GameID = \$1\n";
	$query .= "ORDER BY RollID DESC;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	while ($row = pg_fetch_row($result)) {
		array_push($response["rolls"], array(
			"name"     => $row[0],
			"roll"     => $row[1],
			"result"   => $row[2],
			"details"  => $row[3],
			"isMaster" => $row[4]
		));
	}

	return true;
}

function database_get_game_name(&$response, $gameID) {
	global $db;

	$query = "SELECT Name FROM Games\n";
	$query .= "WHERE GameID = \$1;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$row = null;

	if (!($row = pg_fetch_row($result))) {
		$response["ok"] = "false";
		$response["error"] = "Invalid game ID";
		return false;
	}

	$response["gameName"] = $row[0];

	return true;
}

function database_create_game(&$response, $gameName, $passcode) {
	global $db, $masterPasscode;

	if ($passcode === $masterPasscode) {
		$response["ok"] = "false";
		$response["error"] = "Invalid passcode";
		return false;
	}

	$gameID = uniqid();
	$gameSecret = "" . my_rand();

	$query = "INSERT INTO Games\n";
	$query .= "VALUES ('$gameID', \$1, \$2, '$gameSecret');";

	if (!pg_query_params($db, $query, array($gameName, $passcode))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$response["gameID"] = $gameID;
	$response["gameSecret"] = $gameSecret;

	return true;
}

function database_create_character(&$response, $gameID, $characterName) {
	global $db, $isMaster;

	$characterID = uniqid();
	$characterSecret = "" . my_rand();

	$query = "INSERT INTO Characters\n";
	$query .= "VALUES ('$characterID', \$1, \$2, $isMaster, '$characterSecret');";

	if (!pg_query_params($db, $query, array($gameID, $characterName))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$response["characterID"] = $characterID;
	$response["characterSecret"] = $characterSecret;

	return true;
}

function database_valid_game_id(&$response, $gameID) {
	global $db;

	$query = "SELECT Name FROM Games\n";
	$query .= "WHERE GameID = \$1;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (!pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Invalid game ID";
		return false;
	}

	return true;
}

function database_valid_game_passcode(&$response, $gameID, $passcode) {
	global $db, $masterPasscode, $isMaster;

	if ($passcode === $masterPasscode) {
		$isMaster = "TRUE";
		return true;
	}

	$query = "SELECT Name FROM Games\n";
	$query .= "WHERE GameID = \$1 AND Passcode = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID, $passcode)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (!pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Invalid game passcode";
		return false;
	}

	return true;
}

function database_valid_game_secret(&$response, $gameID, $gameSecret) {
	global $db;

	$query = "SELECT Name FROM Games\n";
	$query .= "WHERE GameID = \$1 AND Secret = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID, $gameSecret)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (!pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Invalid game secret";
		return false;
	}

	return true;
}

function database_valid_character_id(&$response, $gameID, $characterID) {
	global $db, $isMaster;

	$query = "SELECT Name FROM Characters\n";
	$query .= "WHERE CharacterID = \$1 AND GameID = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($characterID, $gameID)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$row = null;

	if (!($row = pg_fetch_row($result))) {
		$response["ok"] = "false";
		$response["error"] = "Invalid character ID";
		return false;
	}

	if ($row[0] === "spelledare") {
		$isMaster = "TRUE";
	}

	return true;
}

function database_valid_character_secret(&$response, $characterID, $characterSecret) {
	global $db;

	$query = "SELECT Name FROM Characters\n";
	$query .= "WHERE CharacterID = \$1 AND Secret = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($characterID, $characterSecret)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (!pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Invalid character secret";
		return false;
	}

	return true;
}

function database_available_character_name(&$response, $gameID, $characterName) {
	global $db, $isMaster;

	if ($characterName === "spelledare") {
		$isMaster = "TRUE";
	}

	$query = "SELECT Name FROM Characters\n";
	$query .= "WHERE GameID = \$1 AND Name = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID, $characterName)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Character is already in the game";
		return false;
	}

	return true;
}

function database_get_game_secret(&$response, $gameID) {
	global $db, $isMaster;

	if ($isMaster !== "TRUE") {
		$response["ok"] = "false";
		$response["error"] = "Unauthorised access to game secret";
		return false;
	}

	$query = "SELECT Secret FROM Games\n";
	$query .= "WHERE GameID = \$1;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$row = null;

	if (!($row = pg_fetch_row($result))) {
		$response["ok"] = "false";
		$response["error"] = "Invalid game ID";
		return false;
	}

	$response["gameSecret"] = $row[0];

	return true;
}

function database_destroy_game(&$response, $gameID, $gameSecret) {
	global $db;

	$query = "SELECT Name FROM Games\n";
	$query .= "WHERE GameID = \$1 AND Secret = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($gameID, $gameSecret)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (!pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Invalid game credentials";
		return false;
	}

	$query = "DELETE FROM Rolls\n";
	$query .= "WHERE GameID = \$1;";

	if (!pg_query_params($db, $query, array($gameID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$query = "DELETE FROM Characters\n";
	$query .= "WHERE GameID = \$1;";

	if (!pg_query_params($db, $query, array($gameID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$query = "DELETE FROM Games\n";
	$query .= "WHERE GameID = \$1;";

	if (!pg_query_params($db, $query, array($gameID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	return true;
}

function database_destroy_character(&$response, $characterID, $characterSecret) {
	global $db;

	$query = "SELECT * FROM Characters\n";
	$query .= "WHERE CharacterID = \$1 AND Secret = \$2;";

	$result = null;

	if (!($result = pg_query_params($db, $query, array($characterID, $characterSecret)))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	if (!pg_fetch_row($result)) {
		$response["ok"] = "false";
		$response["error"] = "Invalid character credentials";
		return false;
	}

	$query = "DELETE FROM Rolls\n";
	$query .= "WHERE CharacterID = \$1;";

	if (!pg_query_params($db, $query, array($characterID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$query = "DELETE FROM Characters\n";
	$query .= "WHERE CharacterID = \$1;";

	if (!pg_query_params($db, $query, array($characterID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	return true;
}

function database_kick(&$response, $kickID) {
	global $db;

	$query = "DELETE FROM Rolls\n";
	$query .= "WHERE CharacterID = \$1;";

	if (!pg_query_params($db, $query, array($kickID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	$query = "DELETE FROM Characters\n";
	$query .= "WHERE CharacterID = \$1;";

	if (!pg_query_params($db, $query, array($kickID))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	return true;
}

function database_add_roll(&$response, $gameID, $characterID, $rollString, $rollResult, $rollDetails) {
	global $db;

	$rollID = uniqid();

	$query = "INSERT INTO Rolls\n";
	$query .= "VALUES (\$1, \$2, \$3, \$4, \$5, \$6);";

	if (!pg_query_params($db, $query, array($rollID, $gameID, $characterID, $rollString, $rollResult, $rollDetails))) {
		$response["ok"] = "false";
		$response["error"] = pg_last_error();
		return false;
	}

	return true;
}
?>
