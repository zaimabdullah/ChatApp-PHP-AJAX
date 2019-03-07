<?php 

//fetch_user.php

include('database_connection.php');

session_start();

$query = "
	SELECT * FROM login
	WHERE user_id != '".$_SESSION['user_id']."'
";
$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$output = '
	<table class="table table-bordered table-striped">
		<tr>
			<td width="60%">Username</td>
			<td width="20%">Status</td>
			<td width="20%">Action</td>
		</tr>
';

foreach ($result as $row) {
	$status = '';
	$current_timestamp = strtotime(date('Y-m-d H:i:s') . '-10 second');
	$current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
	$user_last_activity = fetch_user_last_activity($row['user_id'], $connect);
	if($user_last_activity > $current_timestamp)
	{
		$status = '<span class="alert alert-success" role="alert">Online</span>';
	}
	else
	{
		$status = '<span class="alert alert-danger" role="alert">Offline</span>';
	}
	$output .= '
		<tr>
			<td>'.$row['username'].' '.count_unseen_message($row['user_id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['user_id'], $connect).'</td>
			<td>'.$status.'</td>
			<td><button type="button" class="btn btn-primary btn-xs start_chat" data-touserid="'.$row['user_id'].'" data-tousername="'.$row['username'].'">Start Chat</button></td>
		</tr>	
	';// count_unseen_message to display notification of unread message
}

$output .= '</table>';// titik jgn tggal nanti xklua output

echo $output;
 ?>