<?php
require_once('../libs/Storage.php');

$db = new Storage();

if(isset($_GET['daten'])){
  $_GET['daten'] = urldecode(stripslashes($_GET['daten']));
  $daten['lists'] = $_GET['daten'];
  $daten['name'] = $_GET['name'];
  $daten['created_at'] = date("Y-m-d H:i:s");
  $db->insert('playlist',$daten);
}elseif(isset($_GET['playlist_id'])) {
  $id = $_GET['playlist_id'];
  echo $id;
  $db->delete('playlist','id='.$id);
}
?>
