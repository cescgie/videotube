<?php
require_once('../libs/Storage.php');

$db = new Storage();

$key = $_GET['key'];

$search = $db->select("SELECT * FROM yapi WHERE title LIKE '%'$key'%'");
if($check[0]['count']!=0){
  $print_r($search);
}

?>
