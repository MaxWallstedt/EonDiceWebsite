<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "error" => "");

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

$characterID = $_POST["characterid"];
$characterSecret = $_POST["charactersecret"];

database_connect($res) or die(json_encode($res));
database_destroy_character($res, $characterID, $characterSecret) or die(json_encode($res));

echo json_encode($res);
?>
