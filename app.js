var app = angular.module('JukeTubeApp', []);


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

app.service('VideosService', ['$window', '$rootScope', '$log', function ($window, $rootScope, $log) {

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
  var upcoming = [
    {id: 'O3UBOOZw-FE', title: 'KOLLEGAH - Alpha (Official HD Video)'},
    {id: 'HbtGDZf9Ts8', title: 'KOLLEGAH - King (Official HD Video)'},
    {id: 'yqhsqnNYR4k', title: 'KOLLEGAH - Universalgenie (Official HD Video)'},
    {id: 'FpOOXSd9IxY', title: 'KOLLEGAH - Du bist Boss (Official HD Video)'},
    {id: 'nyrcAPJSRJc', title: 'Kollegah - Mondfinsternis (Official HD Video)'},
    {id: '7tdZx0gMAR0', title: 'Kollegah feat. Sahin - Du (Official HD Video)'}
  ];
  var history = [
    {id: 'JMcbCoCWtd4', title: 'Kollegah feat. Favorite - Discospeed (Official Video)'}
  ];

  $window.onYouTubeIframeAPIReady = function () {
    $log.info('Youtube API is ready');
    youtube.ready = true;
    service.bindPlayer('placeholder');
    service.loadPlayer();
    $rootScope.$apply();
  };

  function onYoutubeReady (event) {
    $log.info('YouTube Player is ready');
    youtube.player.cueVideoById(history[0].id);
    youtube.videoId = history[0].id;
    youtube.videoTitle = history[0].title;
  }

  function onYoutubeStateChange (event) {
    if (event.data == YT.PlayerState.PLAYING) {
      youtube.state = 'playing';
    } else if (event.data == YT.PlayerState.PAUSED) {
      youtube.state = 'paused';
    } else if (event.data == YT.PlayerState.ENDED) {
      youtube.state = 'ended';
      service.launchPlayer(upcoming[0].id, upcoming[0].title);
      service.archiveVideo(upcoming[0].id, upcoming[0].title);
      service.deleteVideo(upcoming, upcoming[0].id);
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

}]);

app.controller('VideosController', function ($scope, $http, $log, VideosService) {

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
      console.log("success get List");
      $scope.upcoming = data;
    });
  }

  init();

  function init() {
      $scope.youtube = VideosService.getYoutube();
      $scope.results = VideosService.getResults();
      $scope.upcoming = VideosService.getUpcoming();
      $scope.history = VideosService.getHistory();
      $scope.playlist = true;
      //get Playlist
      $http.get('ajax/getListPlaylist.php')
      .success( function (data) {
        console.log(data);
        $scope.listplaylist = data;
      })
      .error( function () {
        console.log("error");
      });
  }

  $scope.launch = function (id, title) {
      VideosService.launchPlayer(id, title);
      VideosService.archiveVideo(id, title);
      //VideosService.deleteVideo($scope.upcoming, id);
      $('.item-title').css("background","");
      $('.item-title').css("color","");
      $('#item-title-'+id).css("background","#1171A2");
      $('#item-title-'+id).css("color","#fff");
      $log.info('Launched id:' + id + ' and title:' + title);
    };

    $scope.queue = function (id, title) {
      VideosService.queueVideo(id, title);
      VideosService.deleteVideo($scope.history, id);
      $log.info('Queued id:' + id + ' and title:' + title);
    };

    $scope.delete = function (list, id) {
      VideosService.deleteVideo(list, id);
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
      $scope.youtube = VideosService.getYoutube();
      $scope.results = VideosService.getResults();
      $scope.history = VideosService.getHistory();
      $scope.upcoming = data;
      $scope.playlist = true;
      console.log("sortiert");
    }

});
