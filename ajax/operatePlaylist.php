<?php
require_once('../libs/Storage.php');

$db = new Storage();

if(isset($_GET['action'])){
  if($_GET['action'] == 'create'){
    $_GET['daten'] = urldecode(stripslashes($_GET['daten']));
    $daten['lists'] = $_GET['daten'];
    $daten['name'] = $_GET['name'];
    $daten['created_at'] = date("Y-m-d H:i:s");
    $db->insert('playlist',$daten);
  }elseif($_GET['action'] == 'delete') {
    $id = $_GET['playlist_id'];
    echo $id;
    $db->delete('playlist','id='.$id);
  }elseif($_GET['action'] == 'update') {
    $_GET['daten'] = urldecode(stripslashes($_GET['daten']));
    $daten['lists'] = $_GET['daten'];
    $daten['name'] = $_GET['name'];
    $daten['updated_at'] = date("Y-m-d H:i:s");
    $db->update('playlist',$daten,'name="'.$daten["name"].'"');
  }elseif($_GET['action'] == 'check_name') {
    $name = $_GET['name'];
    $result = $db->select("SELECT EXISTS(SELECT 1 FROM playlist WHERE name ='$name' LIMIT 1) as checked");
    /*
    * if exists result is 1 else is 0
    */
    print_r($result[0]['checked']);
  }
}
?>
