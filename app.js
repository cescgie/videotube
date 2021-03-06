var app = angular.module('App', []);

app.run(function () {
  var tag = document.createElement('script');
  tag.src = "http://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
});

// Config
app.config( function ($httpProvider) {
  delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

// Service
app.service('VideosService', ['$window', '$rootScope', '$log', 'filterFilter', function ($window, $rootScope, $log, filterFilter) {

  var service = this;

  var youtube = {
    ready: false,
    player: null,
    playerId: null,
    videoId: null,
    videoTitle: null,
    playerHeight: '480',
    playerWidth: '640',
    state: 'stopped'
  };
  var results = [];
  var upcoming;
  var history = [];
  var currentidx;

  var Clock = {
    totalSeconds: 0,

    start: function (totalSeconds) {
      var self = this;
      if (this.interval) clearInterval(this.interval);

      this.interval = setInterval(function () {
        totalSeconds += 1;

        /*$("#hour").text(Math.floor(self.totalSeconds / 3600));
        $("#min").text(Math.floor(self.totalSeconds / 60 % 60));
        $("#sec").text(parseInt(self.totalSeconds % 60));*/
        $('#current_time').text(secondsToHms(totalSeconds));
        $('#slidertime').val(totalSeconds);
      }, 1000);
    },

    pause: function () {
      clearInterval(this.interval);
      delete this.interval;
    },

    resume: function (currentseconds) {
      if (!this.interval) this.start(currentseconds);
    }

  };

  function getplaylistupcome() {
    var playlist_name = key;
    return $.ajax({
          url: 'ajax/getPlaylist.php',
          data:{name:playlist_name},
          dataType: 'json',
          success: function(data) {
            console.log("getplaylistupcome");
            console.log(data);
            upcoming = data;
          },
          error: function() {
            console.log('Error occured');
        }
    });
  }

  if(key!=0){
    upcoming = getplaylistupcome();
  }else{
    upcoming = [
      {id: 'O3UBOOZw-FE', title: 'KOLLEGAH - Alpha (Official HD Video)'},
      {id: 'HbtGDZf9Ts8', title: 'KOLLEGAH - King (Official HD Video)'},
      {id: 'yqhsqnNYR4k', title: 'KOLLEGAH - Universalgenie (Official HD Video)'},
      {id: 'FpOOXSd9IxY', title: 'KOLLEGAH - Du bist Boss (Official HD Video)'},
      {id: 'nyrcAPJSRJc', title: 'Kollegah - Mondfinsternis (Official HD Video)'},
      {id: '7tdZx0gMAR0', title: 'Kollegah feat. Sahin - Du (Official HD Video)'}
    ];
  }

  $window.onYouTubeIframeAPIReady = function () {
    $log.info('Youtube API is ready');
    youtube.ready = true;
    service.bindPlayer('placeholder');
    service.loadPlayer();
    console.log("keyku : "+key);
    $rootScope.$apply();
  };

  function onYoutubeReady (event) {
    $log.info('YouTube Player is ready');
    youtube.player.cueVideoById(upcoming[0].id);
    youtube.videoId = upcoming[0].id;
    youtube.videoTitle = upcoming[0].title;
    //change background color
    $('.item-title').css("background","");
    $('.item-title').css("color","");
    $('#item-title-'+youtube.videoId).css("background","#1171A2");
    $('#item-title-'+youtube.videoId).css("color","#fff");
  }
  function onYoutubeStateChange (event) {
    var getDuration = youtube.player.getDuration();
    var getCurrentTime = youtube.player.getCurrentTime();
    var total_time = secondsToHms(getDuration);
    var current_time = secondsToHms(getCurrentTime);
    $('#slidertime').val(getCurrentTime);
    $('#total_time').text(total_time);
    $('#current_time').text(current_time);
    console.log('durasi : '+hmsToSeconds(total_time));

    $("#slidertime").attr({"max" : getDuration});

    if (event.data == YT.PlayerState.PLAYING) {
      youtube.state = 'playing';
      Clock.start(getCurrentTime);
      console.log('playing');
      $('#progressing').addClass('indeterminate');
      $('#playFirstNavigation').hide();
      $('#playNavigation').hide();
      $('#pauseNavigation').show();
    } else if (event.data == YT.PlayerState.PAUSED) {
      youtube.state = 'paused';
      Clock.pause(getCurrentTime);
      console.log('paused');
      $('#progressing').removeClass('indeterminate');
      $('#pauseNavigation').hide();
      $('#playNavigation').show();
    } else if (event.data == YT.PlayerState.ENDED) {
      youtube.state = 'ended';
      Clock.pause(getCurrentTime);
      $('#pauseNavigation').hide();
      $('#playFirstNavigation').show();
      $('#progressing').removeClass('indeterminate');
      //get youtube id currentLaunch
      var yidcurrentLaunch = history[0].id;
      console.log("currentLaunch from onYoutubeStateChange : "+yidcurrentLaunch);
      //update upcoming
      upcoming = service.getCurrentUpcoming();
      //get specific object from yidcurrentLaunch
      var playdex = filterFilter(upcoming , {id: yidcurrentLaunch});
      //get idx from yidcurrentlaunch
      console.log("getCurrentIdx : "+playdex[0].idx);

      //next video
      var index;
      //check if repeat one video aktiv
      var state1 = $('#repeatOneNavigation').attr('state');
      //if state 1, repeat one video aktiv
      if(state1==1){
        index = (playdex[0].idx);
      }else{
        index = (playdex[0].idx)+1;
        if(index==upcoming.length){
          //check if repeat playlist aktiv
          var state2 = $('#repeatNavigation').attr('state');
          //if state 1, playlist repeat aktiv
          if(state2==1){
            index=0;
          }
        }
      }

      console.log("next video index : "+index);
      //add new class to currentLaunch for change background
      $('#item-title-'+upcoming[index].id).addClass("item-"+index);
      //Launch Video
      service.launchPlayer(upcoming[index].id, upcoming[index].title);
      service.archiveVideo(upcoming[index].id, upcoming[index].title);
      console.log("onYoutubeStateChange success");
      //change background color
      $('.item-title').css("background","");
      $('.item-title').css("color","");
      $('.item-'+index).css("background","#1171A2");
      $('.item-'+index).css("color","#fff");

      //updateViewerAnzahl
      service.updateViewerAnzahl(upcoming[index].id);
    }
    $rootScope.$apply();
  }

  function secondsToHms(d) {
    var d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);
    return ((h > 0 ? h + ":" + (m < 10 ? "0" : "") : "") + m + ":" + (s < 10 ? "0" : "") + s);
  }

  function hmsToSeconds(str) {
    var p = str.split(':'),
        s = 0, m = 1;

    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }
    return s;
  }

  this.bindPlayer = function (elementId) {
    $log.info('Binding to ' + elementId);
    youtube.playerId = elementId;
  };

  this.createPlayer = function () {
    $log.info('Creating a new Youtube player for DOM id ' + youtube.playerId + ' and video ' + youtube.videoId);
    return new YT.Player(youtube.playerId, {
      height: youtube.playerHeight,
      width: youtube.playerWidth,
      playerVars: {
        rel: 0,
        showinfo: 0
      },
      events: {
        'onReady': onYoutubeReady,
        'onStateChange': onYoutubeStateChange
      }
    });
  };

  this.loadPlayer = function () {
    if (youtube.ready && youtube.playerId) {
      if (youtube.player) {
        youtube.player.destroy();
      }
      youtube.player = service.createPlayer();
      console.log("loadPlayer");
    }
  };

  this.launchPlayer = function (id, title) {
    youtube.player.loadVideoById({videoId:id,startSeconds:0});
    youtube.videoId = id;
    youtube.videoTitle = title;
    return youtube;
  }

  this.listResults = function (data) {
    results.length = 0;

    for (var i = data.length - 1; i >= 0; i--) {
      results.push({
        id: data[i].yid,
        title: data[i].title,
        description: data[i].description,
        thumbnail: data[i].thumbnail,
        author: data[i].author
      });
    }
    return results;
  }

  this.queueVideo = function (id, title) {
    console.log(id);
    upcoming.push({
      id: id,
      title: title
    });
    return upcoming;
  };

  this.archiveVideo = function (id, title) {
    history.unshift({
      id: id,
      title: title
    });
    return history;
  };

  this.deleteVideo = function (list, id) {
    for (var i = list.length - 1; i >= 0; i--) {
      if (list[i].id === id) {
        list.splice(i, 1);
        break;
      }
    }
  };

  this.getYoutube = function () {
    return youtube;
  };

  this.getResults = function () {
    return results;
  };

  this.getUpcoming = function () {
    return upcoming;
  };

  this.getHistory = function () {
    return history;
  };

  this.updateUpcoming = function(data){
    upcoming = data;
    console.log("this.updateUpcoming");
    var playlist = JSON.stringify(data);
    return upcoming;
  }

  this.getCurrentUpcoming = function(){
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
      strfy.push(js);
    }
    return strfy;
  }

  this.getFirstLeftVideo = function(){
    if ($(window).width() < 600) {
      $('#col_results').hide();
    }else{
      $('#col_results').show();
    }
    $.ajax({
      type: "GET",
      url: "ajax/getLeftVideo.php",
      dataType: "html",
      success: function(response){
          $('#mehr-videos-button').css("display","block");
          $('#results').html(response);
          console.log("success");
      }
    });
  }

  this.updateViewerAnzahl = function (id){
    console.log('updateViewerAnzahl '+id);
    var action = 'update_viewer';
    $.ajax({
      type: "GET",
      url: "ajax/operatePlaylist.php",
      data: { id : id,
              action: action},
      dataType: "html",
      success: function(response){
          console.log("updateViewerAnzahl success");
          console.log(response);
      }
    });
  }

  this.pauseVideo = function(){
    youtube.player.pauseVideo();
  }

  this.playVideo = function(){
    youtube.player.playVideo();
  }

  this.stopVideo = function(){
    youtube.player.stopVideo();
  }

  this.seekTo = function(seconds){
    youtube.player.seekTo(seconds,true);
  }

  this.muteVol = function(){
    if(youtube.player.isMuted()){
      youtube.player.unMute();
      $('#unMuteNavigation').hide();
      $('#muteNavigation').show();
    }else{
      youtube.player.mute();
      $('#muteNavigation').hide();
      $('#unMuteNavigation').show();
    }
  }

}]);

app.controller('VideosController', function ($scope, $http, $log, VideosService, $rootScope, filterFilter) {

  //updateVideo();

  function updateVideo(){
    console.log("updating video loading...");
    $http.post("ajax/updateVideo.php").success(function(data){
      console.log("success update video");
    });
  };

  $scope.updateVideo = function () {
    console.log("updating video loading...");
    $('#update_button_id').text('Updating video...')
    $http.post("ajax/updateVideo.php").success(function(data){
      $('#update_button_id').text('Update success!')
      console.log("success update video");
    });
  };

  $scope.initPlaylist = function (name){
    console.log("initPlaylist");
    $scope.youtube = VideosService.getYoutube();
    $scope.results = VideosService.getFirstLeftVideo();
    $scope.history = VideosService.getHistory();
    $scope.playlist = true;
    //init playlist
    $http.get('ajax/getPlaylist.php', {
      params: {
        name : name
      }
    })
    .success( function (data) {
      //console.log(datax);
      console.log("success get List");
      $scope.upcoming = data;
    });
  }

  init();

  function init() {
      console.log('init');
      $scope.youtube = VideosService.getYoutube();
      //$scope.results = VideosService.getFirstLeftVideo();
      $scope.history = VideosService.getHistory();
      $scope.upcoming = VideosService.getUpcoming();
      $scope.playlist = true;
      //get list of playlists
      $http.get('ajax/getListPlaylist.php')
      .success( function (data) {
        console.log('success get listplaylist');
        $scope.listplaylist = data;
      })
      .error( function () {
        console.log("error");
      });
      //get list of neue videos
      $http.get('ajax/getNeueVideos.php')
      .success( function (data) {
        console.log('success get neue videos');
        $('#new_videos').html(data);
      })
      .error( function () {
        console.log("error");
      });
  }

  $scope.launch = function (id, title) {
      //get current upcoming
      VideosService.launchPlayer(id, title);
      VideosService.archiveVideo(id, title);

      var yidcurrentLaunch = id;

      console.log("currentLaunch : "+yidcurrentLaunch);
      //VideosService.deleteVideo($scope.upcoming, id);
      $('.item-title').css("background","");
      $('.item-title').css("color","");
      $('#item-title-'+id).css("background","#1171A2");
      $('#item-title-'+id).css("color","#fff");

      $log.info('Launched id:' + id + ' and title:' + title);

      //collapse results if launch a video on screen max-width:600px
      if ($(window).width() < 600) {
          $('#col_results').hide();
      }

      //updateViewerAnzahl
      VideosService.updateViewerAnzahl(id);

      //update title
      $('#current_playing_title').text(title);

      //update image
      $http.get('ajax/getCurrentPlayed.php', {
        params: {
          yid: id,
        }
      })
      .success( function (data) {
        var thumbnail = data[0]['thumbnail'];
        console.log(thumbnail);
        $('#current_playing_thumbnail').html('<img src="'+thumbnail+'" width="45px" height="45px">');
      })
      .error( function () {
        console.log("error");
      });

      //update repeat one state
      $('#repeatOneNavigation').attr('state',0);
      $('#repeatOneNavigation').css('color','#fff');

      //update repeat playlist state
      $('#repeatNavigation').attr('state',0);
      $('#repeatNavigation').css('color','#fff');
    };

    $scope.queue = function (id, title) {
      var upcoming = VideosService.queueVideo(id, title);
      //VideosService.deleteVideo($scope.history, id);
      $log.info('Queued id:' + id + ' and title:' + title);
      //update playlist on ui
      $scope.upcoming = upcoming;
      //update latest upcoming
      VideosService.updateUpcoming(upcoming);
    };

    $scope.delete = function (yid) {
      var upcoming = VideosService.getCurrentUpcoming();
      //get specific object from yid
      var playdex = filterFilter(upcoming , {id: yid});
      //get idx from yid
      console.log("getCurrentIdxToRemove : "+playdex[0].idx);
      //remove referenced id
      upcoming.splice(playdex[0].idx, 1);
      //update playlist on ui
      $scope.upcoming = upcoming;
      //update latest upcoming
      VideosService.updateUpcoming(upcoming);

      var currentPlayed = VideosService.getHistory();
      console.log("currentPlayed : "+currentPlayed[0].id);
      $('#item-title-'+currentPlayed[0].id).css("background","#1171A2");
      $('#item-title-'+currentPlayed[0].id).css("color","#fff");
    };

    $scope.search = function () {
      console.log(this.query);
      $http.get('ajax/getVideo.php', {
        params: {
          maxResults: '25',
          q: this.query
        }
      })
      .success( function (data) {
        //console.log(data);
        VideosService.listResults(data);
        $log.info(data);
      })
      .error( function () {
        console.log("error");
        $log.info('Search error');
      });
    }
    $scope.tabulate = function (state) {
      $scope.playlist = state;
    }

    $scope.dragPlaylist = function (data){
      //console.log("$scope.dragPlaylist");
      var playlist = JSON.stringify(data);
      //console.log(playlist);
      VideosService.updateUpcoming(data);
    }

    $scope.getListPlaylist = function (){
      $http.get('ajax/getListPlaylist.php')
      .success( function (data) {
        console.log(data);
        $scope.listplaylist = data;
      })
      .error( function () {
        console.log("error");
      });
    }

    $scope.playNav = function(){
      var currentPlayed = VideosService.getHistory();
      console.log(currentPlayed);
      if (currentPlayed!='' && currentPlayed!=null ) {
        console.log('ada currentPlayed');
        $scope.launch(currentPlayed[0]['id'],currentPlayed[0]['title']);
      }else{
        console.log('no currentPlayed');
        var upcoming = VideosService.getCurrentUpcoming();
        $scope.launch(upcoming[0]['id'],upcoming[0]['title']);
      }
    }

    $scope.pauseVideoNav = function(){
      VideosService.pauseVideo();
    }

    $scope.playVideoNav = function(){
      VideosService.playVideo();
    }

    $scope.stopVideoNav = function(){
      console.log("stopVideoNav");
      VideosService.stopVideo();
    }

    $scope.prevVideoNav = function(){
      console.log('prev');
      var history = VideosService.getHistory();
      if(history.length == 0){
        var current = VideosService.getCurrentUpcoming();
        var yidcurrentLaunch = current[0].id;
        console.log("currentLaunch from onYoutubeStateChange : "+yidcurrentLaunch);
      }else{
        var yidcurrentLaunch = history[0].id;
        console.log("currentLaunch from onYoutubeStateChange : "+yidcurrentLaunch);
      }

      //update upcoming
      upcoming = VideosService.getCurrentUpcoming();
      //get specific object from yidcurrentLaunch
      var playdex = filterFilter(upcoming , {id: yidcurrentLaunch});
      //get idx from yidcurrentlaunch
      console.log("getCurrentIdx : "+playdex[0].idx);
      //next video
      var index = (playdex[0].idx)-1;
      if(playdex[0].idx==0){
        index=upcoming.length-1;
      }
      console.log("previous video index : "+index);
      var playdex2 = filterFilter(upcoming , {idx: index});
      $scope.launch(playdex2[0]['id'],playdex2[0]['title']);
    }

    $scope.nextVideoNav = function(){
      console.log('next');
      var history = VideosService.getHistory();
      if(history.length == 0){
        var current = VideosService.getCurrentUpcoming();
        var yidcurrentLaunch = current[0].id;
        console.log("currentLaunch from onYoutubeStateChange : "+yidcurrentLaunch);
      }else{
        var yidcurrentLaunch = history[0].id;
        console.log("currentLaunch from onYoutubeStateChange : "+yidcurrentLaunch);
      }

      //update upcoming
      upcoming = VideosService.getCurrentUpcoming();
      //get specific object from yidcurrentLaunch
      var playdex = filterFilter(upcoming , {id: yidcurrentLaunch});
      //get idx from yidcurrentlaunch
      console.log("getCurrentIdx : "+playdex[0].idx);
      //next video
      var index = (playdex[0].idx)+1;
      if(index==upcoming.length){
        index=0;
      }
      console.log("next video index : "+index);
      var playdex2 = filterFilter(upcoming , {idx: index});
      $scope.launch(playdex2[0]['id'],playdex2[0]['title']);
    }

    $scope.repeatVideoNav = function(){
      var state = $('#repeatNavigation').attr('state');
      if(state==0){
        console.log('repeat aktif');
        $('#repeatNavigation').attr('state',1);
        $('#repeatNavigation').css('color','#ee6e73');
      }else{
        console.log('repeat non aktif');
        $('#repeatNavigation').attr('state',0);
        $('#repeatNavigation').css('color','#fff');
      }
    }

    $scope.repeatOneVideoNav = function(){
      var state = $('#repeatOneNavigation').attr('state');
      if(state==0){
        console.log('repeat one aktif');
        $('#repeatOneNavigation').attr('state',1);
        $('#repeatOneNavigation').css('color','#ee6e73');
      }else{
        console.log('repeat one non aktif');
        $('#repeatOneNavigation').attr('state',0);
        $('#repeatOneNavigation').css('color','#fff');
      }
    }

    $scope.replayNav = function(){
      //update upcoming
      upcoming = VideosService.getCurrentUpcoming();
      $scope.launch(upcoming[0]['id'],upcoming[0]['title']);
    }

    $scope.seekVideo = function(seconds){
      VideosService.seekTo(seconds);
    }

    $scope.muteVolume = function(){
      VideosService.muteVol();
    }

    $scope.unmuteVolume = function(){
      VideosService.muteVol();
    }
});
