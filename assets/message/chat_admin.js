"use strict";

var timeout = '';
var msg_count = [];

$('#myLink').on('click', function(){ 
    var to_id = $('#to_id').val();
    var chat_input = $('#chat_input').val();
    var from_id = $('#from_id').val();

    $.ajax({
        type: "POST",
        url: base_url+'admin/chat/create',
        data: "from_id="+from_id+"&to_id="+to_id+"&message="+encodeURIComponent(chat_input), 
        dataType: 'json',
        success: function(result) {
                    console.log(result);
            $('#chat_input').val(''); 
            
        	if(result['error'] == false){
              if(result['data']){
                var message = result['data'];
                    var chatitem = '<div class="chat-item chat-right" style=""><div class="chat-details m-0"><div class="chat-text">'+message+'</div></div></div>'; 
                    
                    $(".chat-content").append(chatitem);
                  
              }   
          }else{
              toastr.error('Error is here');
          }
            
        }
    });

});


function get_chat(opposite_user_id){
    console.log(opposite_user_id);
  
	$.ajax({
        type: "POST",
        url: base_url+'admin/chat/get_chat', 
        data: "opposite_user_id="+opposite_user_id,
        dataType: "json",
        success: function(result) 
        {	
    console.log(result['data']);
            
        	if(result['error'] == false){
              if(result['data']){
                var chats = result['data'];
                  msg_count[opposite_user_id] = chats.length;
                  for(var i = 0; i < chats.length; i++) {
                    var chatitem = '<div class="chat-item chat-'+chats[i].position+'" style=""><div class="chat-details m-0"><div class="chat-text">'+chats[i].text+'</div></div></div>'; 
                    
                    console.log(chatitem);
                    $(".chat-content").append(chatitem);
                  }
              }
          }else{
              toastr.error('No messages from this User!');
          }
        }        
    });
    
}

$(document).on('click','.user-selected-for-chat',function(e){
  e.preventDefault();
    $(".chat-content").html('');
    clearTimeout(timeout);
    var card = $('#mychatbox');
    
    var id = $(this).data("id");
    console.log(id);
    msg_count[id] = '';
    $.ajax({
        type: "POST",
        url: base_url+'admin/chat/ajax_get_user_by_id', 
        data: "id="+id,
        dataType: "json",
        success: function(result) 
        {	
            
          get_chat(result['data'].userid);

          if(result['error'] == false){
            $("#to_id").val(result['data'].userid);
              var profile = '<figure class="avatar avatar-sm">'+
              '<img src="'+base_url+'public/uploads/users/'+result['data'].imagelocation+'" alt="'+result['data'].name+'" width="40" height="40">'+
              '</figure><div class="media-body">'+
              '<div class="ml-2 font-weight-bold">'+result['data'].name+'</div>'+
              '</div>';
                
            $("#current_chating_user").html(profile);
            $("#chat-form").removeClass('d-none');
          }else{
              toastr.error('Something wrong! Try again.');
          }
          
            

        }        
    });
});

