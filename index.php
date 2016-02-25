<?php if(isset($_GET['playlist_name'])){
  $key=$_GET['playlist_name'];
  $key=strtolower($key);
}else{
  $key=0;
}?>
<!DOCTYPE html>
<html data-ng-app="App">
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
      var key = "<?= $key?>";
      console.log("key : "+key);
    </script>
    <script type="text/javascript" src="app.js"></script>
    <?php include_once('piwikpixel.php');?>
  </head>
  <body id="myctrl" data-ng-controller="VideosController">
      <div class="progress center-align">
         <div id="progressing" class=""></div>
      </div>
      <div class="row">
        <div id="col_results" class="col s12 l6 m6">
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
                <i class="small material-icons right item-delete" data-ng-click="delete(video.id)">highlight_off</i>
                <p class="item-title" id="item-title-{{video.id}}" yid="{{video.id}}" title="{{video.title}}">{{video.title}}</p>
                <input class="item-id" type="hidden" name="id" value="{{video.id}}">
                <input class="item-idx-{{video.id}}" type="hidden" name="idx" idx="idx-{{video.id}}" value="{{$index + 1}}">
              </li>
            </ol>
          </div>
          <!-- Modal form create/update trigger -->
          <?php if(!isset($_GET['playlist_name'])):?>
          <a class="waves-effect waves-light btn modal-trigger" href="#speichern">Playlist speichern</a>
          <?php else:?>
          <a class="waves-effect waves-light btn modal-trigger" href="#speichern">Playlist aktualisieren</a>
          <?php endif;?>

          <!-- Modal form for create/update playlist Structure -->
          <div id="speichern" class="modal">
            <div class="modal-content">
              <?php if(!isset($_GET['playlist_name'])):?>
                <p>Geben Sie einen Name und ein Kennwort für die neue Playlist</p>
              <?php else:?>
                <p>Playlist aktualisieren</p>
              <?php endif;?><br>
              <div class="row">
                <form id="form_playlist" class="col s12">
                  <div class="row">
                    <div class="input-field col l6 m6 s12">
                      <input id="name" type="text" class="validate" value="<?php if(isset($_GET['playlist_name'])){echo ucfirst($key);} ?>">
                      <label for="name"><p>Name</p></label>
                    </div>
                    <div class="input-field col l6 m6 s12">
                      <input id="password" type="password" class="validate">
                      <label for="password"><p>Password</p></label>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="modal-footer">
              <?php if(!isset($_GET['playlist_name'])):?>
              <a id="config_playlist" href="javascript:void(0)" class="modal-action modal-close waves-effect waves-green btn-flat">Speichern</a>
              <?php else:?>
              <a id="config_playlist" href="javascript:void(0)" class="modal-action modal-close waves-effect waves-green btn-flat">Aktualisieren</a>
              <?php endif;?>
            </div>
          </div>

          <!-- Modal form for delete playlist -->
          <div id="delete_form" class="modal">
            <div class="modal-content">
              <div class="row">
                <form id="form_remove" class="col s12">
                  <p id="delete_form_text"></p>
                  <div class="row">
                    <div class="input-field col l6 m6 s12">
                      <input id="password" type="password" class="validate">
                      <label for="password"><p>Password</p></label>
                      <input type="hidden" name="playlist_name" id="playlist_name">
                      <input type="hidden" name="playlist_id" id="playlist_id">
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="modal-footer">
              <a id="delete_check" href="javascript:void(0)" class="modal-close waves-effect waves-green btn-flat">Entfernen</a>
              <a href="javascript:void(0)" class="modal-close waves-effect waves-green btn-flat">Abbrechen</a>
            </div>
          </div>

          <!-- Modal check form create/update playlist -->
          <div id="check_form" class="modal">
            <div class="modal-content">
              <p id="check_form_text"></p>
            </div>
            <div class="modal-footer">
              <a id="check_form_zurrueck" href="javascript:void(0)" class=" modal-action modal-close waves-effect waves-green btn-flat">Zurrück</a>
            </div>
          </div>

          <!-- Modal success -->
          <div id="success_modal" class="modal">
            <div class="modal-content">
              <p id="success_modal_text"></p>
            </div>
            <div class="modal-footer">
              <a href="javascript:void(0)" class=" modal-action modal-close waves-effect waves-green btn-flat">Schließen</a>
            </div>
          </div>

          <!-- Modal Structure -->
          <div id="check_pass_form" class="modal">
            <div class="modal-content">
              <p id="check_pass_form_text"></p>
            </div>
            <div class="modal-footer">
              <a id="check_pass_zurrueck" href="javascript:void(0)" class=" modal-action modal-close waves-effect waves-green btn-flat">Zurrück</a>
            </div>
          </div>

        </div>
      </div>
      <div class="valign-wrapper">
        <!-- Modal playlist trigger -->
       <a class="waves-effect waves-light btn modal-trigger" href="#modal1" style="margin-right:10px;">Playlists anzeigen</a>

       <!-- Modal playlist -->
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
                        <th class="right" data-field="option">Option</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr data-ng-repeat="playlist in listplaylist">
                        <td><a href="?playlist_name={{ playlist.name }}" class="tooltipped" data-position="right" data-delay="50" data-tooltip="Playlist öffnen">{{ playlist.name | uppercase}}</a></td>
                        <td><a id="delete_button" playlist_id="{{ playlist.id }}" playlist_name="{{ playlist.name }}" class="modal-trigger right" ><i class="small material-icons right tooltipped" data-position="left" data-delay="50" data-tooltip="Playlist entfernen">highlight_off</i></a></td>
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

      <div class="player-navigation">
        <div class="row">
          <div class="col l3">
            <p id="current_playing_title"></p>
          </div>
          <div id="button_player" class="col l9">
            <i id="prevNavigation" class="small material-icons">skip_previous</i>
            <i id="playFirstNavigation" class="small material-icons">play_arrow</i>
            <i id="playNavigation" class="small material-icons" style="display:none;">play_arrow</i>
            <i id="pauseNavigation" class="small material-icons" style="display:none;">pause</i>
            <i id="nextNavigation" class="small material-icons">skip_next</i>
            <i id="repeatOneNavigation" class="small material-icons" state="0">repeat_one</i>
            <i id="repeatNavigation" class="small material-icons" state="0">repeat</i>
            <i id="replayNavigation" class="small material-icons">replay</i>
          </div>
        </div>
      </div>
    <script type="text/javascript" src="libs/control.js"></script>
  </body>
</html>
