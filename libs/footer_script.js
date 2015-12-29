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
