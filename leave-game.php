<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

array_key_exists("characterid", $_POST) or die("<p>Missing 'characterid' parameter</p>");
array_key_exists("charactersecret", $_POST) or die("<p>Missing 'charactersecret' parameter</p>");

$characterID = $_POST["characterid"];
$characterSecret = $_POST["charactersecret"];

$res = array("ok" => "true", "error" => "");

database_connect($res) or die("<p>" . $res["error"] . "</p>");
database_destroy_character($res, $characterID, $characterSecret) or die("<p>" . $res["error"] . "</p>");

header("Location: .");
?>
