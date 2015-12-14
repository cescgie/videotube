<?php
require_once('../libs/Storage.php');

$db = new Storage();
$q = '%';
if(isset($_GET['q']) ){
  $key = $_GET['q'];
}
$result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' AND LOWER(title) LIKE LOWER('%$key%') LIMIT 25 ");
$i = 1;
foreach ($result as $keys => $value) {
  echo
   '<div id="leftVideo'.$i.'" class="video" onclick="video_click('.$i.')" video_yid="'.$value["yid"].'" video_title="'.$value["title"].'">
    <img class="video-image" src="'.$value['thumbnail'].'">
    <p class="video-title">'.$value['title'].'</p>
    <p class="video-author">'.$value['author'].'</p>
    <p class="video-description">'.$value['description'].'</p>
    </div>
   ';
   $i++;
}
 ?>
 <script type="text/javascript">
   function video_click(i){
     var video_yid = $("#leftVideo"+i).attr('video_yid');
     var video_title = $("#leftVideo"+i).attr('video_title');
     console.log(video_yid+' - '+video_title);
     angular.element($("#myctrl")).scope().queue(video_yid,video_title);
     var item = [];
     $('#upcoming li p.item-title').each(function (i, e) {
         item.push($(e).text());
     });
     var item2 = [];
     $('#upcoming li input.item-id').each(function (i, e) {
         item2.push($(e).val());
     });
     var strfy = [];
     for (var i = 0; i < item.length; i++) {
       var js = {id:item2[i],title:item[i]};
       strfy.push(js);
     }
     strfy.push({"id":video_yid,"title":video_title});
     //var playlist = JSON.stringify(strfy);
     //console.log(playlist);
     //$("#playlist").load("/ #playlist");
     var scope = angular.element($("#upcoming")).scope();
     scope.$apply(function(){
          //$scope.youtube = VideosService.getYoutube();
          //$scope.results = VideosService.getResults();
          scope.upcoming = strfy;
     });
   }
 </script>
