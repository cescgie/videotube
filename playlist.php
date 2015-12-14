<?php
if(isset($_GET['name'])){
  $key = $_GET['name'];
  echo $key;
}
?>
<!DOCTYPE html>
<html data-ng-app="playlistApp">
  <head>
    <meta charset="utf-8">
    <title>VideoTube</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/libs/style.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  </head>
  <body id="playlistctrl" data-ng-controller="VideosController" data-ng-init="something('<?php echo $key; ?>')">
    <header>
      <h1><a href="/" style="color:white;">Video<strong>Tube</strong></a></h1>
    </header>
    <!-- Left Video -->
    <div id="results"></div>

    <div id="player">
      <div id="placeholder" ></div>
    </div>
    <div id="playlist">
      <p id="current" style="position:relative">{{ youtube.videoTitle }}</p>
      <ol id="upcoming" class="sortable" data-ng-show="playlist">
        <li data-ng-repeat="video in upcoming">
          <p class="item-play" data-ng-click="launch(video.id, video.title)">play</p>
          <p class="item-delete" data-ng-click="delete(upcoming, video.id)">delete</p>
          <p class="item-title" id="item-title-{{video.id}}">{{video.title}}</p>
          <input class="item-id" type="hidden" name="name" value="{{video.id}}">
        </li>
      </ol>
      <p id="tabs">
        <a ng-class="{on:playlist}" data-ng-click="tabulate(true)">Upcoming</a>
        <a ng-class="{on:!playlist}" data-ng-click="tabulate(false)">History</a>
      </p>
    </div>

    <script type="text/javascript" src="/libs/angular.min.js"></script>
    <script type="text/javascript" src="/app_playlist.js"></script>

    <script>
      $(function() {
        $( ".sortable" ).sortable({
          stop: function() {
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
              //var jsons= JSON.stringify(js);
              strfy.push(js);
            }
            //var playlist = JSON.stringify(strfy);
            console.log(strfy);
            angular.element($("#myctrl")).scope().dragPlaylist(strfy);
          }
        });
      });
    </script>
  </body>
</html>