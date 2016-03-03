$("form input").keypress(function (e) {
  if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
      $('button[type=submit] .default').click();
      $('#col_results').show();
      console.log($('#query').val());
      $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "ajax/getToSuggestVideo.php",
        data: {q:$('#query').val()},
        dataType: "html",   //expect html to be returned
        success: function(response){
            $('#mehr-videos-button').css("display","block");
            $('#results').html(response);
            console.log("success");
        }
      });
      return false;
  } else {
      return true;
  }
});

$(document).on('click','#db_update',function(){
  console.log('db_update');
  $.ajax({
    type: "GET",
    url: "ajax/updateVideo.php",
    dataType: "html",
    success: function(response){
        console.log("success");
        Materialize.toast('Datenbank aktualisiert!', 3000, 'rounded');
    }
  });
});

$(document).on('click','#empf_video',function(){
  console.log('empf_video');
  $(".empf_video_div").toggle();
});

$(document).ready(function(){
  $('.modal-trigger-db').leanModal();
  $(".button-collapse").sideNav();
  update_neues_video();
  most_viewed_videos();
  //get_list_videos();
});

function most_viewed_videos(){
  $.ajax({
    type: "GET",
    url: "ajax/adminpush.php",
    data:{action:'most_viewed_video'},
    dataType: "html",
    success: function(response){
        $('.most_viewed_list').html(response);
    }
  });
}
function update_neues_video(){
  $.ajax({
    type: "GET",
    url: "ajax/adminpush.php",
    data: {action:'neues_video'},
    dataType: "html",
    success: function(response){
        $('#new_videos').html(response);
    }
  });
}


function get_list_videos(){
  $.ajax({
    type: "GET",
    url: "ajax/getLeftVideo.php",
    dataType: "html",
    success: function(response){
        $('#list_videos').html(response);
    }
  });
}

$("form input#listvideos").keypress(function (e) {
  if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
      $('button[type=submit] .default').click();
      $('#col_results').show();
      console.log($('#listvideos').val());
      $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "ajax/adminpush.php",
        data: {action:'list_video',q:$('#listvideos').val()},
        dataType: "html",   //expect html to be returned
        success: function(response){
            $('#mehr-videos-button').css("display","block");
            $('#list_videos').html(response);
            console.log("success");
        }
      });
      return false;
  } else {
      return true;
  }
});
