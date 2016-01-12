<!DOCTYPE html>
<html data-ng-app="JukeTubeApp">
  <head>
    <meta charset="utf-8">
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Own style -->
    <link rel="stylesheet" href="libs/style.css" type="text/css">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="libs/materialize/css/materialize.min.css"  media="screen,projection"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="libs/materialize/js/materialize.min.js"></script>
  </head>
  <body id="myctrladmin" data-ng-controller="VideosController">
    <div class="container">
      <h1>admin</h1>
      <a href="index.php"><-home</a><br><br>
      <a class="modal-trigger-db waves-effect waves-light btn" href="#db_update_modal">Datenbank aktualisieren</a> <span id="status_db"></span><br><br>
      <!-- Modal Structure -->
      <div id="db_update_modal" class="modal">
         <div class="modal-content">
           <p>Möchten Sie die Datenbank aktualisieren?</p>
         </div>
         <div class="modal-footer">
           <a id="db_update" href="#" class=" modal-action modal-close waves-effect waves-green btn-flat">Ja</a>
         </div>
       </div>

       <a id="most_viewed_video" class="waves-effect waves-light btn" href="#">Top 10 meist geschaute Videos</a><br><br>

       <div class="most_viewed_video_div" style="display:none;">
         <div class="most_viewed_list">
         </div><br>
       </div>
      <a id="empf_video" class="waves-effect waves-light btn" href="#">Empfohlenes Video aktualisieren</a><br><br>
      <div class="empf_video_div" style="display:none">
        <nav>
          <div class="nav-wrapper">
            <form>
              <div class="input-field">
                <input id="query" name="q" type="search" placeholder="Video suchen" data-ng-model="query" required>
                <label for="search"><i id="search_icon" class="material-icons">search</i></label>
              </div>
            </form>
          </div>
        </nav>
        <div class="row">
          <div id="col_results" class="col s12 l12 m12">
            <div id="results"></div>
          </div>
        </div>
      </div>

    </div><!-- Container -->
    <script type="text/javascript">
    $("form input").keypress(function (e) {
      if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
          $('button[type=submit] .default').click();
          $('#col_results').show();
          console.log($('#query').val());
          $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "ajax/getToSuggestVideo.php",
            data: {q:$('#query').val()},
            dataType: "html",   //expect html to be returned
            success: function(response){
                $('#mehr-videos-button').css("display","block");
                $('#results').html(response);
                console.log("success");
            }
          });
          return false;
      } else {
          return true;
      }
    });

    $(document).on('click','#db_update',function(){
      console.log('db_update');
      $.ajax({
        type: "GET",
        url: "ajax/updateVideo.php",
        dataType: "html",
        success: function(response){
            console.log("success");
            Materialize.toast('Datenbank aktualisiert!', 3000, 'rounded');
        }
      });
    });

    $(document).on('click','#empf_video',function(){
      console.log('empf_video');
      $(".empf_video_div").toggle();
    });

    $(document).ready(function(){
      $('.modal-trigger-db').leanModal();
    });

    $(document).on('click','#most_viewed_video',function(){
      console.log('most_viewed_video');
      $(".most_viewed_video_div").toggle();
      $.ajax({
        type: "GET",
        url: "ajax/getMostShown.php",
        dataType: "html",
        success: function(response){
            console.log("success");

		        $('.most_viewed_list').html(response);
        }
      });
    });
    </script>
  </body>
</html>
