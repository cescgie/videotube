<?php
require_once('../libs/Storage.php');

$db = new Storage();
$key = strtolower($_GET['name']);

$result = $db->select("SELECT lists FROM playlist WHERE LOWER(name) LIKE LOWER('$key') ");
print_r($result[0]['lists']);
?>
