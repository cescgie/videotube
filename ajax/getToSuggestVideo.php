<?php
require_once('../libs/Storage.php');

$db = new Storage();
$q = '%';
if(isset($_GET['q']) && $_GET['q'] != ''){
  $key = $_GET['q'];
  $result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND BINARY LOWER(title) LIKE LOWER('%$key%') OR REPLACE(title, ' ', '') LIKE LOWER('%$key%')  AND suggest != 1 LIMIT 25");
}else{
  $key = '%';
  $result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND suggest != 1 ORDER BY id DESC LIMIT 25");
}
$resultq = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND suggest = 1 LIMIT 1");
$i = 1;
foreach ($resultq as $key => $value) {
  # code...
  echo
   '<div id="leftVideo'.$i.'" more_id="'.$i.'" key="'.$key.'" class="video" video_yid="'.$value["yid"].'" old_empf_id="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    <p class="video-title">'.$value['title'].' (<i class="material-icons" style="position:relative;bottom:-0.2em;font-size:1.2em;">visibility</i> '.$value['viewers'].' Views) <span class="right" style="background-color:yellow;color:black">EMPFOHLENES VIDEO</span></p>
    <p class="video-author">'.$value['author'].'<a class="waves-effect waves-light btn modal-trigger right" href="#suggest'.$i.'">Setzt als empfohlenes Video</a></p>
    </div>
   ';
   echo '
   <!-- Modal check form create/update playlist -->
   <div id="suggest'.$i.'" class="modal">
     <div class="modal-content">
       <p id="check_form_text">Setzt "'.$value['title'].'" als empfohlenes Video?</p>
     </div>
     <div class="modal-footer">
       <a id="confirm_suggest" yid="'.$value["yid"].'" href="javascript:void(0)" class=" modal-action modal-close waves-effect waves-green btn-flat">Ja</a>
     </div>
   </div>
   ';
}
$i = 2;
foreach ($result as $keys => $value) {
  echo
   '<div id="leftVideo'.$i.'" more_id="'.$i.'" key="'.$key.'" class="video" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    <p class="video-title">'.$value['title'].' (<i class="material-icons" style="position:relative;bottom:-0.2em;font-size:1.2em;">visibility</i> '.$value['viewers'].' Views)</p>
    <p class="video-author">'.$value['author'].'<a class="waves-effect waves-light btn modal-trigger right" href="#suggest'.$i.'">Setzt als empfohlenes Video</a></p>
    </div>
   ';
   echo '
   <!-- Modal check form create/update playlist -->
   <div id="suggest'.$i.'" class="modal">
     <div class="modal-content">
      <p id="check_form_text">Setzt "'.$value['title'].'" als empfohlenes Video?</p>
     </div>
     <div class="modal-footer">
       <a id="confirm_suggest" yid="'.$value["yid"].'" href="javascript:void(0)" class=" modal-action modal-close waves-effect waves-green btn-flat">Ja</a>
     </div>
   </div>
   ';
   $i++;
}
 ?>
 <script type="text/javascript">
 $(document).ready(function(){
   $('.modal-trigger').leanModal();
 });
 $(document).on('click','#confirm_suggest',function(){
   console.log('confirm_suggest');
   var old_empf = $('.video').attr('old_empf_id');
   if(old_empf=='undefined'){
     old_empf = '%';
   }
   console.log('old empf_video : '+old_empf);
   var yid = $(this).attr('yid');
   console.log('yid : '+yid);
   var action = "update_empf_videos";
   $.ajax({
     type: "GET",
     url: "ajax/operatePlaylist.php",
     data: { old_empf_id : old_empf,
             new_empf_id: yid,
             action: action},
     dataType: "html",
     success: function(data){
       console.log('success update suggest video');
     }
   });
 });
 </script>
