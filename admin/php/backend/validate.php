<?php

if(session_status() == PHP_SESSION_NONE)
	session_start();

require_once "input_handler.php";
require_once "connection.php";
require_once "cipher.php";

$username = encrypt(encode($_POST['username']));
$password = encrypt(encode($_POST['password']));

$query = "SELECT admin_id, admin_type, state FROM admin WHERE username = '$username' AND password = '$password';";
$result = $conn->query($query);

if($result->num_rows < 1) {
	$_SESSION['error_msg'] = "Invalid username or password";
	header("Location: ../login.php");
	exit;
}
else if($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	if($row['state'] == 0) {
		$_SESSION['error_msg'] = "The account is currently inactive";
		header("Location: ../login.php");
		exit;
	}

	//record user login
	$query = "UPDATE admin_activity SET last_login = CURRENT_TIMESTAMP() WHERE admin_id = ".$row['admin_id'];
	if(!$conn->query($query)) {
		$_SESSION['error_msg'] = "Server error. Please contact the developers.";
		header("Location: ../login.php");
		exit;
	}

	$_SESSION['id'] = $row["admin_id"];
	$_SESSION['admin-type'] = $row["admin_type"];
	$_SESSION['college'] = $row["college"];

	if($_SESSION['admin-type'] == 1)
		header("Location: ../administrators.php");
	else
		header("Location: ../eventstory.php");
	exit;
}

?>