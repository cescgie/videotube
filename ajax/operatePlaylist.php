<?php
require_once('../libs/Storage.php');
require_once('../libs/password.php');

$db = new Storage();
$password = new Password();

if(isset($_GET['action'])){
  if($_GET['action'] == 'create'){
    $_GET['daten'] = urldecode(stripslashes($_GET['daten']));
    $daten['lists'] = $_GET['daten'];
    $daten['name'] = strtolower($_GET['name']);
    $daten['password'] = $password->hash($_GET['password']);
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
  }elseif($_GET['action'] == 'check_password') {
    $name = $_GET['name'];
    $pass = $_GET['password'];
    $result = $db->select("SELECT password FROM playlist WHERE name='$name' ");
    if($password->validate($pass,$result[0]['password'])){
      echo 1;
    }else{
      echo 0;
    }
  }elseif ($_GET['action'] == 'update_viewer') {
    # code...
    $id = $_GET['id'];
    $viewers = $db->select("SELECT * FROM yapi WHERE yid='$id' ");
    $viewer['viewers'] = $viewers[0]['viewers']+1;
    $db->update('yapi',$viewer,'yid="'.$id.'"');
    echo $viewers[0]['viewers'];
  }
}
?>
