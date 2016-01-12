<?php
require_once('../libs/Storage.php');

$db = new Storage();
$result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' ORDER BY viewers DESC LIMIT 10");

foreach ($result as $keys => $value) {
  echo
   '<div class="video" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    ';
	if($value['viewers']==0 || $value['viewers']==1){
	echo
    '<p class="video-title">'.$value['title'].'  (<i class="material-icons" style="position:relative;bottom:-0.2em;font-size:1.2em;">visibility</i> '.$value['viewers'].' View)</p>';
	}else{
	echo
    '<p class="video-title">'.$value['title'].'  (<i class="material-icons" style="position:relative;bottom:-0.2em;font-size:1.2em;">visibility</i> '.$value['viewers'].' Views)</p>';
	}
	echo
    '<p class="video-author">'.$value['author'].'</p>
    <p class="video-description">'.$value['description'].'</p>
    </div>
   ';
}
?>
