/*
* on ready function
*/
  $(document).ready(function(){
    /*
  	* trigger modal
  	*/
    $('.modal-trigger').leanModal();
    /*
    * prevent spaces in input name
    */
    $("form input#name").on({
      keydown: function(e) {
        if (e.which === 32)
          return false;
      },
      change: function() {
        this.value = this.value.replace(/\s/g, "");
      }
    });
    /*
    * Warn user before leave or close the page
    */
    window.onbeforeunload = function(){
      return 'Sie haben noch nicht die aktuelle Playlist gespeichert!';
    };
  });
/*
* close icon on search
*/
  $(document).on('click','#close_result',function(){
    $('#results').empty();
    if ($(window).width() < 600) {
      $('#col_results').hide();
    }
  });
/*
* click search icon and results
*/
  $(document).on('click','#search_icon',function(){
      var q =  $('#query').val();
      console.log("q : "+q);
      $('#col_results').show();
      $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "ajax/getLeftVideo.php",
        data: {q:q},
        dataType: "html",   //expect html to be returned
        success: function(response){
            $('#col_results').show();
            $('#mehr-videos-button').css("display","block");
            $('#results').html(response);
            console.log("success");
        }
      });
  });
/*
* trigger leftVideo after input search and give results
*/
  $("form input").keypress(function (e) {
    if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
        $('button[type=submit] .default').click();
        $('#col_results').show();
        console.log($('#query').val());
        $.ajax({    //create an ajax request to load_page.php
          type: "GET",
          url: "ajax/getLeftVideo.php",
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
/*
* playlist drag and drop sortable
*/
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
/*
* pop up for save playlist
*/
$(document).on('click','#config_playlist',function(){
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
  var playlist_name = $('form#form_playlist #name').val();
  var password = $('form#form_playlist #password').val();
  if (playlist_name!='' && playlist_name !=null && password!='' && password !=null) {
    console.log(playlist_name+'_'+password);
    if(key!=0){
      var playlist_name = key;
      var action = 'check_password';
      $.ajax({
        type: "GET",
        url: "ajax/operatePlaylist.php",
        data: { name : playlist_name,
                password : password,
                action: action},
        dataType: "html",
        success: function(response){
            if(response!=1){
              $('#check_form_text').text('Password falsch!');
              $('#check_form').openModal();
            }else{
              var new_action = 'update';
              operatePlaylist(playlist,playlist_name,password,new_action);
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
      });
    }else{
      console.log('speichern');
      var action = 'check_name';
      $.ajax({
        type: "GET",
        url: "ajax/operatePlaylist.php",
        data: { name : playlist_name,
                action: action},
        dataType: "html",
        success: function(data){
            if(data==1){
              $('#check_form_text').text('Playlist mit dem Name "'+playlist_name+'" ist schon existiert!');
              $('#check_form').openModal();
            }else{
              var action = 'create';
              operatePlaylist(playlist,playlist_name,password,action);
            }
        }
      });
    }
  }else{
    $('#check_form_text').text('Bitte alle Felder ausfüllen!');
    $('#check_form').openModal();
  }
});

/*
* Modal for check form
*/
$(document).on('click','#check_form_zurrueck',function(){
  $('#speichern').openModal();
});

/*
* Modal for check password
*/
$(document).on('click','#check_pass_zurrueck',function(){
  $('#delete_form').openModal();
});

/*
* Modal for delete form
*/
$(document).on('click','#delete_button',function(){
  var playlist_name= $(this).attr('playlist_name');
  var playlist_id= $(this).attr('playlist_id');
  $('#delete_form_text').text('Geben Sie das richtige Passwort für Playlist "'+ucwords(playlist_name)+'"');
  $('#playlist_name').val(playlist_name);
  $('#playlist_id').val(playlist_id);
  $('#delete_form').openModal();
});

/*
* Delete confirmation
*/
$(document).on('click','#delete_check',function(){
  var playlist_name= $('form#form_remove #playlist_name').val();
  var playlist_id= $('form#form_remove #playlist_id').val();
  var password = $('form#form_remove #password').val();
  if (password!='' && password !=null) {
    var action = 'check_password';
    $.ajax({
      type: "GET",
      url: "ajax/operatePlaylist.php",
      data: { name : playlist_name,
              password : password,
              action: action},
      dataType: "html",
      success: function(response){
          if(response!=1){
            $('#check_pass_form_text').text('Password falsch!');
            $('#check_pass_form').openModal();
          }else{
            var new_action = 'delete';
            $.ajax({
              type: "GET",
              url: "ajax/operatePlaylist.php",
              data: { playlist_id : playlist_id,
                      action: new_action},
              dataType: "html",
              success: function(response){
                  angular.element($("#myctrl")).scope().getListPlaylist();
                  $('#success_modal_text').text('Playlist "'+playlist_name+'" entfernt!');
                  $('#success_modal').openModal();
              },
              error: function (request, status, error) {
                  alert(request.responseText);
              }
            });
          }
      },
      error: function (request, status, error) {
          alert(request.responseText);
      }
    });
  }else{
    $('#check_pass_form_text').text('Bitte alle Felder ausfüllen!');
    $('#check_pass_form').openModal();
  }
});


/*
* Function for operate playlist
*/
  function operatePlaylist(playlist,playlist_name,password,action){
    $.ajax({
      type: "GET",
      url: "ajax/operatePlaylist.php",
      data: { daten : playlist,
              name: playlist_name,
              password: password,
              action: action},
      dataType: "html",
      success: function(response){
          angular.element($("#myctrl")).scope().getListPlaylist();
          if(action=='create'){
            $('#success_modal_text').text('Playlist "'+playlist_name+'" gespeichert!');
          }else{
            $('#success_modal_text').text('Playlist "'+playlist_name+'" aktualisiert!');
          }
          $('#success_modal').openModal();
      }
    });
  }

  /*
  * Function for load more video lists on the left
  */

  function loadMore(){
    var lastIndex = $('.video:last').attr('more_id');
    var key = $('.video:last').attr('key');
    console.log('load : '+lastIndex);
    console.log('key : '+key);
    $.ajax({
         type: "POST",
         url : "ajax/getLoadMore.php",
         data: {
                 last_id: lastIndex,
                 key:key
               },
         beforeSend: function(){
           $('#mehr-videos-button').css("display","none");
           $('#mehr-videos-spin').css('display','block');
         },
         success: function(data){
           console.log('success');
           $('#results').append(data);
           $('#mehr-videos-button').css("display","block");
           $('#mehr-videos-spin').css('display','none');
         }
      });
  }

  function ucwords (str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}
