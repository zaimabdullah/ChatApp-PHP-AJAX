<?php 

// index.php

include('database_connection.php');

session_start();

if(!isset($_SESSION['user_id'])) //if user still not login
{
	header("Location:login.php");
}

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mervick/emojionearea/dist/emojionearea.min.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/gh/mervick/emojionearea/dist/emojionearea.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>
	<title>Chat Application Using PHP ajax Jquery</title>
</head>
<body>
	<div class="container">
		<br>

		<h3 align="center">Chat Application Using PHP ajax Jquery</h3>
		<br><br>

		<div class="row">
			<div class="col-md-7 col-sm-5">
				<h4>Online User</h4>
			</div>
			<div class="col-md-2 col-sm-3">
				<input type="hidden" id="is_active_group_chat_window" value="no">
				<button type="button" name="group_chat" id="group_chat" class="btn btn-warning btn-xs">Group Chat</button>
			</div>
			<div class="col-md-2 col-sm-3">
				<p align="right">Hi - <?php echo $_SESSION['username']; ?> - <a href="logout.php">Logout</a></p>
			</div>
		</div>	
		<div class="table-responsive">
			<div id="user_details"></div>
			<div id="user_model_details"></div>
		</div>
	</div>
</body>
</html>

<style>
	.chat_message_area{
		position: relative;
		width: 100%;
		height: auto;
		background-color: #fff;
		border: 1px solid #ccc;
		border-radius: 3px;
	}

	#group_chat_message{
		width: 100%;
		height: auto;
		min-height: 80px;
		overflow: auto;
		padding: 6px 24px 6px 12px;
	}

	.image_upload{
		position: absolute;
		top: 3px;
		right: 3px;
	}

	.image_upload > form > input{
		display: none;
	}

	.image_upload img{
		width: 24px;
		cursor: pointer;
	}
</style>

<div id="group_chat_dialog" title="Group Chat Window">
	<div id="group_chat_history" style="height: 400px;border: 1px solid #ccc;overflow-y: scroll;margin-bottom: 24px;padding: 16px;">
		
	</div>
	<div class="form-group">
		<!-- <textarea name="group_chat_message" id="group_chat_message" class="form-control"></textarea> -->
		<div class="chat_message_area">
			<div id="group_chat_message" contenteditable class="form-control">
				
			</div>
			<div class="image_upload">
				<form action="upload.php" id="uploadImage" method="post">
					<label for="uploadFile"><img src="img/upload.png" alt="picture"></label>
					<input type="file" name="uploadFile" id="uploadFile" accept=".jpg, .png">
				</form>
			</div>
		</div>
	</div>
	<div class="form-group" align="right">
		<button type="button" name="send_group_chat" id="send_group_chat" class="btn btn-secondary">Send</button>
	</div>
</div>

<script>
	$(document).ready(function () {

		fetch_user(); // after done fetch_user.php

		setInterval(function () {
			update_last_activity();
			fetch_user();
			update_chat_history_data();
			fetch_group_chat_history();
		}, 5000);// every 5sec will update the apps table

		function fetch_user () {
			$.ajax({
				url:"fetch_user.php",// engine page
				method:"POST", // request type
				success:function (data) { 
					$('#user_details').html(data); // Sets or returns the content of selected elements at #user_details
				} // function need to run after success
			}) 
		}

		function update_last_activity () {
			$.ajax({
				url:"update_last_activity.php", // bukan semicolon tp ,
				success:function () {
					
				}
			})
		}

		// this is make_chat_dialog_box that create popup chat dialog and it also call fetch_user_chat_history function
		function make_chat_dialog_box (to_user_id, to_user_name) {
			var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
			modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data_touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
			modal_content += fetch_user_chat_history(to_user_id);
			modal_content += '</div>';
			modal_content += '<div class="form-group">';
			modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>';
			modal_content += '</div><div class="form-group" align="right">';
			modal_content += '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-secondary send_chat">Send</button></div></div>';
			$('#user_model_details').html(modal_content);
		}

		// when click on .start_chat button it will call make_chat_dialog_box function for popup chat dialog
		$(document).on('click', '.start_chat', function () {
			var to_user_id = $(this).data('touserid');
			var to_user_name = $(this).data('tousername');
			make_chat_dialog_box(to_user_id, to_user_name);
			$("#user_dialog_"+to_user_id).dialog({
				autoOpen:false,
				width:400
			});
			$('#user_dialog_'+to_user_id).dialog('open');// this .dialog() that make the chat area
			$('#chat_message_'+to_user_id).emojioneArea({
				pickerPosition:"top",
				toneStyle:"bullet"
			});// put emoji charac at chat texting form
		});

		$(document).on('click', '.send_chat', function () {
			var to_user_id = $(this).attr('id');
			var chat_message = $('#chat_message_'+to_user_id).val();
			$.ajax({
				url:"insert_chat.php",
				method:"POST",
				data:{to_user_id:to_user_id, chat_message:chat_message},// define what data we want to send to server
				success:function (data) {
					//$('#chat_message_'+to_user_id).val('');// to empty chat texting form after send the chat(without emoji)
					var element = $('#chat_message_'+to_user_id).emojioneArea();
					element[0].emojioneArea.setText('');// to empty chat texting form after send the chat(with emoji)
					$('#chat_history_'+to_user_id).html(data);// underscore pastikan buat sebelum +
				}
			})
		});

		// this function display chat message on dialog box popup 
		function fetch_user_chat_history (to_user_id) {
			$.ajax({
				url:"fetch_user_chat_history.php",
				method:"POST",
				data:{to_user_id:to_user_id},
				success:function(data){
					$('#chat_history_'+to_user_id).html(data);// pastikan betul ejaan
				}
			}) 
		}

		// to make the chat real time
		function update_chat_history_data () {
			$('.chat_history').each(function () {
				var to_user_id = $(this).data('touserid');
				fetch_user_chat_history(to_user_id);
			});
		}

		// $(document).on('click', '.ui-button-icon', function () {
		// 	$('..user_dialog').dialog('destroy')remove();
		// });

		// for typing... function
		$(document).on('focus', '.chat_message', function(){
			var is_type = 'yes';
			$.ajax({
				url:"update_is_type_status.php",
				method:"POST",
				data:{is_type:is_type},
				success:function () {
						
				}
			}) 
		});

		$(document).on('blur', '.chat_message', function () {
			var is_type = 'no';
			$.ajax({
				url:"update_is_type_status.php",
				method:"POST",
				data:{is_type:is_type},
				success:function () {
					
				}
			})
		});

		$('#group_chat_dialog').dialog({
			autoOpen:false,
			width:400
		});

		$('#group_chat').click(function () {
			$('#group_chat_dialog').dialog('open');
			$('#is_active_group_chat_window').val('yes');
			fetch_group_chat_history();
		});

		$('#send_group_chat').click(function () {
			var chat_message = $('#group_chat_message').html();
			var action = 'insert_data';
			if (chat_message != '') {
				$.ajax({
					url:"group_chat.php",
					method:"POST",
					data:{chat_message:chat_message, action:action},
					success:function (data) {
						$('#group_chat_message').html('');
						$('#group_chat_history').html(data);
					}
				})
			}
		});

		function fetch_group_chat_history () {
			var group_chat_dialog_active = $('#is_active_group_chat_window').val();
			var action = "fetch_data";
			if (group_chat_dialog_active == 'yes') {
				$.ajax({
					url:"group_chat.php",
					method:"POST",
					data:{action:action},
					success:function (data) {
						$('#group_chat_history').html(data);
					}
				})
			}
		}

		$('#uploadFile').on('change', function () {
			$('#uploadImage').ajaxSubmit({
				target: "#group_chat_message",
				resetForm: true
			});
		});

		$(document).on('click', '.remove_chat', function () {
			var chat_message_id = $(this).attr('id');
			if (confirm("Are you sure you want to remove this chat?")) {
				$.ajax({
					url:"remove_chat.php",
					method:"POST",
					data:{chat_message_id:chat_message_id},
					success:function (data) {
						update_chat_history_data();
					}
				})
			}
		});
	});
</script>