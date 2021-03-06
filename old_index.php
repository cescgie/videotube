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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/libs/style.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="libs/angular.min.js"></script>
    <script type="text/javascript">
      console.log("Init key");
      var key = "<?php echo $key;?>";
      console.log("key : "+key);
    </script>
    <script type="text/javascript" src="app.js"></script>
  </head>
  <body id="myctrl" data-ng-controller="VideosController">
    <header>
      <h1><a href="/" style="color:white;">Video<strong>Tube</strong></a></h1>
      <form id="search" data-ng-submit="search()">
        <input id="query" name="q" type="text" placeholder="Search for a YouTube video" data-ng-model="query">
        <input id="submit" type="image" src="/img/search.png" alt="Search">
      </form>
      <nav>
        <a id="play">{{ youtube.state }}</a>
      </nav>
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
          <p class="item-delete" data-ng-click="delete(video.id)">delete</p>
          <p class="item-title" id="item-title-{{video.id}}">{{video.title}}</p>
          <input class="item-id" type="hidden" name="id" value="{{video.id}}">
          <input class="item-idx-{{video.id}}" type="hidden" name="idx" idx="idx-{{video.id}}" value="{{$index + 1}}">
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
    <div id="playlisting">
      <?php if(isset($_GET['playlist_name'])){?>
      <a href="javascript:save('<?php echo $key;?>')">Update</a><br><br>
      <?php }else{?>
      <a href="javascript:save('<?php echo $key;?>')">Save</a><br><br>
      <?php }?>
      <ol>
        <li data-ng-repeat="playlist in listplaylist">
          <a href="index.php?playlist_name={{ playlist.name }}">{{ playlist.name }}</a>
          <a class="remove_playlist" data-ng-click='confirm_delete(playlist.name,playlist.id)'>x</a>
        </li>
      </ol>
    </div>

    <!--<a id="update_button_id" data-ng-click="updateVideo()" type="button">Update Video</a>-->
    <script type="text/javascript">
    $(function() {
      $("form input").keypress(function (e) {
          if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
              $('button[type=submit] .default').click();
              console.log($('#query').val());
              $.ajax({    //create an ajax request to load_page.php
                type: "GET",
                url: "ajax/getLeftVideo.php",
                data: {q:$('#query').val()},
                dataType: "html",   //expect html to be returned
                success: function(response){
                    //console.log(response);
                    $('#results').html(response);
                    console.log("success");
                }
              });
              return false;
          } else {
              return true;
          }
      });
    });
    </script>

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
            var item3 = [];
            $('#upcoming li input.item-idx').each(function (i, e) {
                item3.push($(e).val());
            });
            var strfy = [];
            for (var i = 0; i < item.length; i++) {
              var js = {id:item2[i],title:item[i],idx:i};
              //var jsons= JSON.stringify(js);
              strfy.push(js);
            }
            var playlist = JSON.stringify(strfy);
            console.log(playlist);
            angular.element($("#myctrl")).scope().dragPlaylist(strfy);
          }
        });
      });
    </script>


    <script type="text/javascript">
      function save(key){
        console.log('key to update : '+key);
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
        //console.log(playlist);
        if(key!=0){
          if (confirm("Are you sure to update playlist '"+key+"'?") == true) {
            var playlist_name = key;
            var action = 'update';
            console.log("updating : "+playlist_name);
            operatePlaylist(playlist,playlist_name,action);
          }else{
            alert('Cancel update');
          }
        }else{
          var playlist_name = prompt("Give a name for your new playlist", "");
          if (playlist_name != null && playlist_name != "" ) {
            $.ajax({
              type: "GET",
              url: "ajax/operatePlaylist.php",
              data: { name : playlist_name,
                      action: 'check_name'},
              dataType: "html",
              success: function(data){
                  console.log("check_name : "+playlist_name+" result: "+data);
                  if(data==1){
                    alert("Playlist with name '"+playlist_name+"' already exists!");
                  }else{
                    var action = 'create';
                    console.log("create playlist");
                    operatePlaylist(playlist,playlist_name,action);
                  }
              }
            });
          }else{
            alert("Give a name for new playlist!");
          }
        }
      }

      function operatePlaylist(playlist,playlist_name,action){
        $.ajax({
          type: "GET",
          url: "ajax/operatePlaylist.php",
          data: { daten : playlist,
                  name: playlist_name,
                  action: action},
          dataType: "html",
          success: function(response){
              //angular.element($("#myctrl")).scope().getListPlaylist();
              alert("Playlist '"+playlist_name+"' saved!");
          }
        });
      }
    </script>
  </body>
</html>
