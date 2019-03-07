<?php 

include('database_connection.php');

session_start();

$message = '';

if(isset($_SESSION['user_id'])) //if user have login
{
	header("Location:index.php");
}

if(isset($_POST['login']))
{
	$query = "
		SELECT * FROM  login
		WHERE username = :username
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
			':username' => $_POST["username"]
		)
	);
	$count = $statement->rowCount();
	if($count > 0)
	{
		$result = $statement->fetchAll();
		foreach ($result as $row) {
			if (password_verify($_POST["password"], $row["password"])) {
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['username'] = $row['username'];
				$sub_query = "
					INSERT INTO login_details(user_id)
					VALUES ('".$row['user_id']."')
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute();
				$_SESSION['login_details_id'] = $connect->lastInsertId();
				header('location:index.php');
			}
			else
			{
				$message = '<label>Wrong Password</label>';
			}
		}
	}
	else
	{
		$message = '<label>Wrong Username</label>';
	}
}	
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	<title>Chat Application Using PHP ajax Jquery</title>
</head>
<body>
	<div class="container">
		<br>

		<h3 align="center">Chat Application Using PHP ajax Jquery</h3>
		<br><br>
		<div class="card">
			<div class="card-header">Chat Application Login</div>
			<div class="card-body">
				<p class="text-danger"><?php echo $message; ?></p>
				<form action="" method="post">
					<div class="form-group">
						<label class="card-title">Enter Username</label>
						<input type="text" name="username" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="card-title">Enter Password</label>
						<input type="password" name="password" class="form-control" required>
					</div>
					<div class="form-group">
						<input type="submit" name="login" class="btn btn-primary" value="Login">
					</div>
					<div align="center">
						<a href="register.php">Register</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>