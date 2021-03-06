var app = angular.module('JukeTubeAppList', []);

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
    youtube.player.cueVideoById(history[0].id);
    youtube.videoId = history[0].id;
    youtube.videoTitle = history[0].title;
    //change background color
    $('.item-title').css("background","");
    $('.item-title').css("color","");
    $('#item-title-'+youtube.videoId).css("background","#1171A2");
    $('#item-title-'+youtube.videoId).css("color","#fff");
  }
  function onYoutubeStateChange (event) {
    if (event.data == YT.PlayerState.PLAYING) {
      youtube.state = 'playing';
    } else if (event.data == YT.PlayerState.PAUSED) {
      youtube.state = 'paused';
    } else if (event.data == YT.PlayerState.ENDED) {
      youtube.state = 'ended';
      //get youtube id currentLaunch
      var yidcurrentLaunch = history[0].id;
      console.log("currentLaunch from onYoutubeStateChange : "+yidcurrentLaunch);
      //update upcoming
      upcoming = getCurrentUpcoming();
      //get specific object from yidcurrentLaunch
      var playdex = filterFilter(upcoming , {id: yidcurrentLaunch});
      //get idx from yidcurrentlaunch
      console.log("getCurrentIdx : "+playdex[0].idx);
      //next video
      var index = (playdex[0].idx)+1;
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
    }
    $rootScope.$apply();
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
    youtube.player.loadVideoById(id);
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

  function getCurrentUpcoming(){
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

}]);

app.controller('VideosControllerList', function ($scope, $http, $log, VideosService, $rootScope, filterFilter) {

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
    $scope.results = VideosService.getResults();
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
      $scope.youtube = VideosService.getYoutube();
      $scope.results = VideosService.getResults();
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
  }

  $scope.launch = function (id, title) {
      //get current upcoming
      var data = getCurrentUpcoming();
      VideosService.updateUpcoming(data);
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
    };

    $scope.queue = function (id, title) {
      VideosService.queueVideo(id, title);
      //VideosService.deleteVideo($scope.history, id);
      $log.info('Queued id:' + id + ' and title:' + title);
    };

    $scope.delete = function (yid) {
      var upcoming = getCurrentUpcoming();
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
          maxResults: '10',
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

    function removePlaylist(name,id){
      console.log("Remove playlist "+name);
      $http.get('ajax/operatePlaylist.php', {
        params: {
          playlist_id: id,
          action:'delete'
        }
      })
      .success( function (data) {
        console.log(data);
        angular.element($("#myctrl")).scope().getListPlaylist();
      })
      .error( function () {
        console.log("error");
      });
    }

    $scope.confirm_delete = function(name,id){
      if (confirm("Are you sure to delete this playlist?") == true) {
        removePlaylist(name,id);
      } else {
        console.log("cancel");
      }
    }

    function getCurrentUpcoming(){
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
});
