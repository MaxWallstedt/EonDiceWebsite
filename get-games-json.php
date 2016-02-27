<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

include "database.php";

$res = array("ok" => "true", "games" => array(), "error" => "");

database_connect($res) or die(json_encode($res));
database_get_games($res) or die(json_encode($res));

echo json_encode($res);
?>
