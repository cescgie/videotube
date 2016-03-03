<!DOCTYPE html>
<html data-ng-app="App">
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
    <div class="container"><hr>
		<div class="right-align">
          <a class="modal-trigger-db" href="#db_update_modal"><i class="material-icons">autorenew</i>Datenbank aktualisieren</a></div> <span id="status_db"></span>
      <div class="row">
        <div class="col s12">
          <ul class="tabs">
            <li class="tab col s3"><a class="active"  href="#div_neues_videos" id="id_neues_videos">Neue Videos</a></li>
            <li class="tab col s3"><a href="#div_most_viewed_video">Top 10 Videos</a></li>
            <li class="tab col s3"><a href="#div_empf_video">Empfohlenes Video</a></li>
          </ul>
        </div>
        <hr>

        <div id="div_db_update" class="col s12">
          <!-- Modal Structure -->
          <div id="db_update_modal" class="modal">
             <div class="modal-content">
               <p>Möchten Sie die Datenbank aktualisieren?</p>
             </div>
             <div class="modal-footer">
               <a id="db_update" href="#" class=" modal-action modal-close waves-effect waves-green btn-flat">Ja</a>
             </div>
           </div>
        </div>

        <div id="div_most_viewed_video" class="col s12">
            <div class="most_viewed_list"></div>
        </div>

        <div id="div_empf_video" class="col s12">
          <a id="empf_video" class="waves-effect waves-light btn" href="#">Empfohlenes Video aktualisieren</a><br><br>
          <div class="empf_video_div" style="display:none">
            <nav>
              <div class="nav-wrapper">
                <form>
                  <div class="input-field">
                    <input id="query" name="q" type="search" placeholder="Video suchen" data-ng-model="query" required>
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
        </div>

        <div id="div_neues_videos" class="col s12">
          <div class="row">
            <div class="col l6 list_videos">
              <h5 class="center-align">Neue Video hinzufügen</h5>
              <nav>
                <div class="nav-wrapper">
                  <form>
                    <div class="input-field search_form">
                      <input id="listvideos" name="q" type="search" placeholder="Search" data-ng-model="query" required>
                      <i id="search_icon" class="material-icons">search</i>
                      <!-- <i id="close_result" class="material-icons">closes</i> -->
                    </div>
                  </form>
                </div>
              </nav>
              <div id="list_videos" class="div_list_videos"></div>
            </div>
            <div class="col l6 list_videos">
              <h5 class="center-align">Neue Videos</h5>
              <div id="new_videos" class="div_list_new_videos"></div>
            </div>
          </div>
        </div>

      </div>
    </div><!-- Container -->
    <script type="text/javascript" src="libs/admin.js"></script>
  </body>
</html>
