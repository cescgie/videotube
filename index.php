<!DOCTYPE html>
<html data-ng-app="JukeTubeApp">
  <head>
    <meta charset="utf-8">
    <title>VideoTube</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="libs/style.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

  </head>
  <body  id="myctrl" data-ng-controller="VideosController">
    <header>
      <h1>Video<strong>Tube</strong></h1>
      <form id="search" data-ng-submit="search()">
        <input id="query" name="q" type="text" placeholder="Search for a YouTube video" data-ng-model="query">
        <input id="submit" type="image" src="img/search.png" alt="Search">
      </form>
      <nav>
        <a id="play">{{ youtube.state }}</a>
        <a id="pause">Pause</a>
      </nav>
    </header>
    <div id="results">
      <div class="video" data-ng-repeat="video in results" data-ng-click="queue(video.id, video.title)">
        <img class="video-image" data-ng-src="{{ video.thumbnail }}">
        <p class="video-title">{{ video.title }}</p>
        <p class="video-author">{{ video.author }}</p>
        <p class="video-description">{{ video.description }}</p>
      </div>
    </div>
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
      <ol id="history" class="sortable" data-ng-hide="playlist">
        <li data-ng-repeat="video in history">
          <p class="item-play" data-ng-click="launch(video.id, video.title)">play</p>
          <p class="item-delete" data-ng-click="delete(history, video.id)">delete</p>
          <p class="item-title" data-ng-click="queue(video.id, video.title)">{{video.title}}</p>
        </li>
      </ol>
      <p id="tabs">
        <a ng-class="{on:playlist}" data-ng-click="tabulate(true)">Upcoming</a>
        <a ng-class="{on:!playlist}" data-ng-click="tabulate(false)">History</a>
      </p>
    </div>
    <a href="javascript:save()">Save</a>
    <ol>
      <li data-ng-repeat="playlist in listplaylist">
        <a data-ng-click="initPlaylist(playlist.name)">{{ playlist.name }}</a>
      </li>
    </ol>
    <!--<a id="update_button_id" data-ng-click="updateVideo()" type="button">Update Video</a>-->

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


    <script type="text/javascript">
      function save(){
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
        var playlist = JSON.stringify(strfy);
        console.log(playlist);
        $.ajax({
          type: "GET",
          url: "ajax/savePlaylist.php",
          data: { daten : playlist },
          dataType: "html",
          success: function(response){
              console.log("success");
          }
        });
      }
    </script>
    <script type="text/javascript" src="libs/angular.min.js"></script>
    <script type="text/javascript" src="app.js"></script>
  </body>
</html>
