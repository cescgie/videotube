<?php
require_once('../libs/Storage.php');
$db = new Storage();
if(isset($_GET['action']) && $_GET['action']=='neues_video'){
  $result = $db->select("SELECT * FROM yapi WHERE new = 1 AND title IS NOT NULL AND title != ''");
  $i = 1;
  echo '<div class="row">';
  foreach ($result as $key => $value) {
    echo
     '<div class="col s6 m3 l2">
        <div class="card">
          <div class="card-image">';
          if ($value['new_thumbnail']=='') {
          echo   '<img width="100px" height="100px" src="'.$value['thumbnail'].'">';
          } else {
          echo   '<img width="100px" height="100px" src="'.$value['new_thumbnail'].'">';
          }
          echo
          '</div>
        </div>
        <div class="card-content">
          <p class="limit_text_admin">'.$value['title'].'</p>
          <p class="limit_text_admin">'.$value['author'].'</p>
        </div>
        <div class="card-action">
          <a class="modal-trigger" href="#edit'.$i.'">Edit</a>
        </div>
      </div>
     ';

     ?>
     <!-- Modal check update -->
     <div id="edit<?=$i?>" class="modal">
        <div class="modal-content">
          <form id="form" action="#" onsubmit="return edit_video(<?=$i?>);" enctype="multipart/form-data" method="post">
            <div class="row">
              <div class="input-field col s12">
                <input value="<?=$value['title']?>" id="title<?=$i?>" name="title" type="text" class="validate">
                <label class="active" for="title">Title</label>
              </div>
              <div class="input-field col s12">
                <input value="<?=$value['author']?>" id="author<?=$i?>" name="author" type="text" class="validate">
                <label class="active" for="author">Author</label>
              </div>
              <div class="input-field col s12">
                <div id="thumbnail_picture<?=$i?>">
                  <?php if ($value['new_thumbnail']=='') :?>
                    <img width="100px" height="100px" src="<?=$value['thumbnail']?>">
                  <?php else :?>
                    <img width="100px" height="100px" src="<?=$value['new_thumbnail']?>">
                  <?php endif;?>
                </div>
                <form method="post">
                  <input type="file" id="files<?=$i?>" >
                </form>
              </div>
              <input id="change_img<?=$i?>" value="" name="change_img" type="hidden">
              <input type="hidden" value="<?=$value['id']?>" id="id<?=$i?>" name="id" class="validate">
              <input type="submit" class="right btn submit"  value="Speichern">
            </div>
          </form>
        </div>
      </div>
      <script type="text/javascript">
        var id = "<?= $i ?>";
        document.getElementById('files'+id).addEventListener('change', function(e) {
          var idx = "<?= $i ?>";
          var index = $('#id'+idx).val();
          $('#change_img'+idx).attr('value', 1);

          console.log(idx);
          var formData = new FormData();
          formData.append('file', $('#files'+idx)[0].files[0]);
          $.ajax({
              url : 'ajax/uploadThumbnail.php?id='+index,
              type : 'POST',
              data : formData,
              processData: false,  // tell jQuery not to process the data
              contentType: false,  // tell jQuery not to set contentType
              beforeSend: function() {
                $('#thumbnail_picture'+idx).html('<div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>');
              },
              success : function(response) {
                console.log(response);
                var parsed = JSON.parse(response);
                if(parsed.status==1){
                   $('#thumbnail_picture'+idx).html('<img src="'+parsed.tmp_url+'" alt="Profile Picture"  class="responsive-img" style="width:100px;height:100px">');
                }else{
                  $('#thumbnail_picture'+idx).html('<p style="color:red">Error: Invalid file or file is too big</p>');
              }
          }
        });
      });
      </script>
      <?php
     $i++;
  }
  echo '</div>';
}elseif(isset($_GET['action']) && $_GET['action']=='edit_video'){
  echo 'edit_video';
  $data['title'] = filter_var($_GET['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
  $data['author'] = filter_var($_GET['author'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
  $change_img = filter_var($_GET['change_img'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
  if($change_img != 1){
    $data['new_thumbnail'] = '';
  }
  $id = $_GET['id'];
  $db->update('yapi',$data,'id="'.$id.'"');
}elseif(isset($_GET['action']) && $_GET['action']=='most_viewed_video'){

  $result = $db->select("SELECT * FROM yapi WHERE title IS NOT NULL AND title != '' ORDER BY viewers DESC LIMIT 10");

  foreach ($result as $keys => $value) {
    echo
     '<div class="col s6 m3 l2">
        <div class="card">
          <div class="card-image">'
          ;
        	if($value['viewers']==0 || $value['viewers']==1){
        	echo
            '<p class="video-title"><i class="material-icons" style="position:relative;bottom:-0.2em;font-size:2em;">visibility</i> <span>'.$value['viewers'].' View</p>';
        	}else{
        	echo
            '<p class="video-title"><i class="material-icons" style="position:relative;bottom:-0.2em;font-size:2em;">visibility</i> '.$value['viewers'].' Views</p>';
        	}
        echo
          '<img width="100px" height="100px" src="'.$value['thumbnail'].'">
          </div>
        </div>
        <div class="card-content">
          <p class="limit_text_admin">'.$value['title'].'</p>
          <p class="limit_text_admin">'.$value['author'].'</p>
        </div>
      </div>
     ';
  }
}else{
  echo 'unidentified';
}
?>

<!-- new video -->
<script type="text/javascript">
  $(document).ready(function(){
    $('.modal-trigger').leanModal();
  });

  function edit_video(i){
    var title = $('#title'+i).val();
    var author = $('#author'+i).val();
    var change_img = $('#change_img'+i).val();
    var id = $('#id'+i).val();
    $.ajax({
      type: "GET",
      url: "ajax/adminpush.php",
      data: {action:'edit_video',
            title:title,
            author:author,
            change_img:change_img,
            id:id},
      dataType: "html",
      success: function(response){
          console.log(response);
      }
    });
  }
</script>
