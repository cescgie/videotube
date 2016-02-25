<?php
require_once('../libs/Storage.php');

$db = new Storage();
$q = '%';
if(isset($_GET['q']) && $_GET['q'] != ''){
  $key = $_GET['q'];
  $result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND BINARY LOWER(title) LIKE LOWER('%$key%') OR REPLACE(title, ' ', '') LIKE LOWER('%$key%')  AND suggest != 1 ORDER BY meta_id DESC LIMIT 25");
}else{
  $key = '%';
  $result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != ''  AND suggest != 1 ORDER BY meta_id DESC LIMIT 25");
}
$resultq = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND suggest = 1 LIMIT 1");
$i = 1;
foreach ($resultq as $key => $value) {
  # code...
  echo
   '<div id="leftVideo'.$i.'" more_id="'.$i.'" key="'.$key.'" class="video" onclick="video_click('.$i.')" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    <p class="video-title">'.$value['title'].' (<i class="material-icons" style="position:relative;bottom:-0.2em;font-size:1.2em;">visibility</i> '.$value['viewers'].' Views) <span class="right" style="background-color:yellow;color:black">EMPFOHLENES VIDEO</span></p>
    <p class="video-author">'.$value['author'].'</p>
    <p class="video-description">'.$value['description'].'</p>
    </div>
   ';
}
$i = 2;
foreach ($result as $keys => $value) {
  echo
   '<div id="leftVideo'.$i.'" more_id="'.$i.'" key="'.$key.'" class="video" onclick="video_click('.$i.')" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    <p class="video-title">'.$value['title'].' (<i class="material-icons" style="position:relative;bottom:-0.2em;font-size:1.2em;">visibility</i> '.$value['viewers'].' Views)</p>
    <p class="video-author">'.$value['author'].'</p>
    <p class="video-description">'.$value['description'].'</p>
    </div>
   ';
   $i++;
}
 ?>
