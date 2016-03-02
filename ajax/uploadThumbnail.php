<?php
require_once('../libs/Storage.php');

$db = new Storage();

$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
$index = $_GET['id'];
if ((($_FILES["file"]["type"] == "image/gif")
        || ($_FILES["file"]["type"] == "image/jpeg")
        || ($_FILES["file"]["type"] == "image/jpg")
        || ($_FILES["file"]["type"] == "image/pjpeg")
        || ($_FILES["file"]["type"] == "image/x-png")
        || ($_FILES["file"]["type"] == "image/png"))
        && in_array($extension, $allowedExts)) {
  if ($_FILES["file"]["error"] > 0) {
    echo json_encode($allowedExts);
  }else{
    $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
    $file_name = $_FILES["file"]["name"];
    $tmp_url = 'img/thumbnail/'. $file_name.'_'.date('Y-m-d_H:m:s').'.'.$ext;
    $file_url =  $_SERVER['DOCUMENT_ROOT'].'/videotube/'. $tmp_url;
    $edit['new_thumbnail'] = $tmp_url;

    $data_video = $db->select("SELECT * FROM yapi WHERE id = '".$index."' LIMIT 1");
    $old_picture = $data_video[0]['new_thumbnail'];
    if($old_picture!=''){
      unlink($_SERVER['DOCUMENT_ROOT'].'/videotube/'.$old_picture);
    }
    if(move_uploaded_file($_FILES["file"]["tmp_name"],$file_url)){
      $db->update('yapi',$edit,'id="'.$index.'"');
      $edit['status'] = 1;
      $edit['tmp_url'] = $tmp_url;
      echo json_encode($edit);
    }
  }
} //end of if FILE
?>
