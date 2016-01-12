<?php

require_once('../libs/Storage.php');

$db = new Storage();
$key = '%';
$last_id = '';
if(isset($_POST['key']) && isset($_POST['last_id']) ){
  $key = $_POST['key'];
  $last_id = $_POST['last_id'];
}
$result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND LOWER(title) LIKE LOWER('%$key%') OR REPLACE(title, ' ', '') LIKE LOWER('%$key%') ORDER BY meta_id DESC LIMIT 20 OFFSET $last_id");

$i = $last_id;
foreach ($result as $keys => $value) {
  echo
   '<div id="leftVideo'.$i.'" more_id="'.$i.'" key="'.$key.'" class="video tooltipped" data-position="top" data-delay="50" data-tooltip="in playlist hinzufügen" onclick="video_click('.$i.')" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
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
   $i++;
}
?>
<script type="text/javascript">
$(document).ready(function(){
 $('.tooltipped').tooltip({delay: 50});
});
</script>
