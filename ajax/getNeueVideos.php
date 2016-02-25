<?php
require_once('../libs/Storage.php');

$db = new Storage();

$result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND suggest != 1 ORDER BY meta_id DESC LIMIT 12");
$i = 2;
?>
<div class="row">
<?php
foreach ($result as $keys => $value) {
  echo
   '<div class="col l3 m4 s6 neue_videos" id="leftVideo'.$i.'" onclick="video_click('.$i.')" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img width="100px" height="100px"  src="'.$value['thumbnail'].'">
    <p class="neue-video-title limit_text">'.$value['title'].'</p>
    <p class="neue-video-author">'.$value['author'].'</p>
    </div>
   ';
   $i++;
}
?>
</div>
