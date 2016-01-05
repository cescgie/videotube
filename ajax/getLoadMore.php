<?php

require_once('../libs/Storage.php');

$db = new Storage();
$key = '%';
$last_id = '';
if(isset($_POST['key']) && isset($_POST['last_id']) ){
  $key = $_POST['key'];
  $last_id = $_POST['last_id'];
}
$result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND LOWER(title) LIKE LOWER('%$key%') LIMIT 10 OFFSET $last_id");

$i = $last_id;
foreach ($result as $keys => $value) {
  echo
   '<div id="leftVideo'.$i.'" more_id="'.$i.'" key="'.$key.'" class="video" onclick="video_click('.$i.')" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    <p class="video-title">'.$value['title'].'</p>
    <p class="video-author">'.$value['author'].'</p>
    <p class="video-description">'.$value['description'].'</p>
    </div>
   ';
   $i++;
}
?>
