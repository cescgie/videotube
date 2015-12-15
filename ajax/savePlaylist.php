<?php
require_once('../libs/Storage.php');

$db = new Storage();

$_GET['daten'] = urldecode(stripslashes($_GET['daten']));
$daten['lists'] = $_GET['daten'];
$daten['name'] = $_GET['name'];
$daten['created_at'] = date("Y-m-d H:i:s");
$db->insert('playlist',$daten);

?>
