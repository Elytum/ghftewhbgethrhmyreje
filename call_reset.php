<?php
	if (array_key_exists ('submit' , $_GET) == false ||
		array_key_exists ('newpassword' , $_GET) == false)
		return ;
	if ($_GET['submit'] != "Submit")
		return ;
	
	function check_reset($conn, $email, $token) {
		$valid_reset = $conn->prepare('SELECT COUNT(*) FROM resets WHERE email=?;');
		$valid_reset->bindParam(1, $email);
		$valid_reset->execute();
		$result = $valid_reset->fetchAll();
		if ($result[0][0] == 0) {
			return false;
		}
		else {
			return true;
		}
	}

	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagru";
	try {
		$newpassword = $_GET['newpassword'];
		$email = $_GET['email'];
		$token = $_GET['token'];
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// echo "Connected successfully";
		if (check_reset($conn, $email, $token) == false) {
			echo "Error: Invalid token !";
			return ;
		}
		if (strlen($newpassword) < 8) {
			echo "Error: Password too short!";
			return ;
		}

		if (!preg_match("#[0-9]+#", $newpassword)) {
			echo "Error: Password must include at least one number!";
			return ;
		}

		if (!preg_match("#[a-zA-Z]+#", $newpassword)) {
			echo "Error: Password must include at least one letter!";
			return ;
		}
		$update_user = $conn->prepare('UPDATE users SET password=? WHERE email=?;');
		$update_user->bindParam(1, hash('whirlpool',$newpassword));
		$update_user->bindParam(2, $email);
		$update_user->execute();

		$remove_resets = $conn->prepare('DELETE FROM resets WHERE email=?;');
		$remove_resets->bindParam(1, $email);
		$remove_resets->execute();

		header('Location: /index.php');
		exit();
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>