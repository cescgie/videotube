<?php
require_once('../libs/Storage.php');

$db = new Storage();
$key = $_GET['yid'];
$result = $db->select("SELECT * FROM yapi WHERE yid = '$key'");

echo $json_response = json_encode($result);
?>
