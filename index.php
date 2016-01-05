<?php if(isset($_GET['playlist_name'])){
  $key=$_GET['playlist_name'];
}else{
  $key=0;
}?>
<!DOCTYPE html>
<html data-ng-app="JukeTubeApp">
  <head>
    <meta charset="utf-8">
    <title>VideoTube</title>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Own style -->
    <link rel="stylesheet" href="libs/style.css" type="text/css">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="libs/materialize/css/materialize.min.css"  media="screen,projection"/>
    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <!-- Compiled and minified JavaScript -->
    <script src="libs/materialize/js/materialize.min.js"></script>
    <!-- Angular -->
    <script type="text/javascript" src="libs/angular.min.js"></script>
    <script type="text/javascript">
      console.log("Init key");
      var key = "<?php echo $key;?>";
      console.log("key : "+key);
    </script>
    <script type="text/javascript" src="app.js"></script>
  </head>
  <body id="myctrl" data-ng-controller="VideosController">
      <h1 class="center-align"><a href="index.php" style="color:black;">Video<strong style="color:#ee6e73">Tube</strong></a></h1>
      </nav>
      <nav>
        <div class="nav-wrapper">
          <form>
            <div class="input-field">
              <input id="query" name="q" type="search" placeholder="Video suchen" data-ng-model="query" required>
              <label for="search"><i id="search_icon" class="material-icons">search</i></label>
              <i id="close_result" class="material-icons">closes</i>
            </div>
          </form>
        </div>
      </nav>
      <div class="progress center-align">
         <div id="progressing" class=""></div>
     </div>
      <p class="center-align">{{ youtube.state }}</p>
      <div class="row">
        <div id="col_results" class="col s12 l6 m6">
          <div id="results"></div>
          <a class="mehr-videos-button waves-effect waves-light btn" id="mehr-videos-button" href="javascript:loadMore();" style="display:none;">mehr Videos</a>
          <div id="mehr-videos-spin" class="preloader-wrapper small active center-align" style="display:none;">
            <div class="spinner-layer spinner-red-only ">
              <div class="circle-clipper left">
                <div class="circle"></div>
              </div><div class="gap-patch">
                <div class="circle"></div>
              </div><div class="circle-clipper right">
                <div class="circle"></div>
              </div>
            </div>
          </div>
        </div>
        <div id="col_player" class="col s12 l6 m6">
          <div id="player">
            <div id="placeholder" ></div>
          </div>
          <div id="playlist">
            <p id="current" style="position:relative">{{ youtube.videoTitle }}</p>
            <ol id="upcoming" class="sortable" data-ng-show="playlist">
              <li data-ng-repeat="video in upcoming">
                <p class="item-dex"><p>
                <p class="item-delete" data-ng-click="delete(video.id)">delete</p>
                <p class="item-title" id="item-title-{{video.id}}" data-ng-click="launch(video.id, video.title)">{{video.title}}</p>
                <input class="item-id" type="hidden" name="id" value="{{video.id}}">
                <input class="item-idx-{{video.id}}" type="hidden" name="idx" idx="idx-{{video.id}}" value="{{$index + 1}}">
              </li>
            </ol>
          </div>

          <?php if(isset($_GET['playlist_name'])){?>
            <a class="waves-effect waves-light btn" href="javascript:save('<?php echo $key;?>')">Playlist aktualisieren</a><br><br>
          <?php }else{?>
            <a class="waves-effect waves-light btn" href="javascript:save('<?php echo $key;?>')">Playlist speichern</a><br><br>
          <?php }?>
        </div>
      </div>
      <div class="valign-wrapper">
        <!-- Modal Trigger -->
       <a class="waves-effect waves-light btn modal-trigger" href="#modal1" style="margin-right:10px;">Playlists anzeigen</a>

       <!-- Modal Structure -->
       <div id="modal1" class="modal bottom-sheet">
         <div class="modal-content">
           <div class="container">
             <h4>Playlists</h4>
             <div class="row">
               <div class="col s12 m12 l12">
                 <div id="playlisting">
                   <table class="striped">
                    <thead>
                      <tr>
                        <th data-field="name">Name</th>
                        <th data-field="option">Option</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr data-ng-repeat="playlist in listplaylist">
                        <td><a href="index.php?playlist_name={{ playlist.name }}">{{ playlist.name }}</a></td>
                        <td><a class="remove_playlist" data-ng-click='confirm_delete(playlist.name,playlist.id)'>x</a></td>
                      </tr>
                    </tbody>
                  </table>
                 </div>
               </div>
             </div>
           </div>
         </div>
       </div>
      </div>
    <script type="text/javascript" src="libs/control.js"></script>
    <script type="text/javascript">
    function loadMore(){
      var lastIndex = $('.video:last').attr('more_id');
      var key = $('.video:last').attr('key');
      console.log('load : '+lastIndex);
      console.log('key : '+key);
      $.ajax({
      		 type: "POST",
      		 url : "ajax/getLoadMore.php",
      		 data: {
      						 last_id: lastIndex,
                   key:key
      					 },
           beforeSend: function(){
             $('#mehr-videos-button').css("display","none");
             $('#mehr-videos-spin').css('display','block');
           },
           success: function(data){
             console.log('success');
             $('#results').append(data);
             $('#mehr-videos-button').css("display","block");
             $('#mehr-videos-spin').css('display','none');
           }
        });
    }
    </script>
  </body>
</html>
