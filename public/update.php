<?php
	$token = $_POST['token'];
	$newemail = $_POST['newemail'];
	$newpassword = $_POST['newpassword'];
	$password = $_POST['password'];

	function valid_logs($email, $password)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return "Warning: Invalid email format"; 
		}
		if (strlen($password) < 8) {
			return "Warning: Password too short!";
		}

		if (!preg_match("#[0-9]+#", $password)) {
			return "Warning: Password must include at least one number!";
		}

		if (!preg_match("#[a-zA-Z]+#", $password)) {
			return "Warning: Password must include at least one letter!";
		}
		return null;
	}
/*NOT READY*/
	function update_user($conn, $email, $password, $newemail, $newpassword)
	{
		$check_user = $conn->prepare('SELECT password FROM users WHERE email=?');
		$check_user->bindParam(1, $email);
		$check_user->execute();
		$result = $check_user->fetchAll();
		if (count($result) != 1)
		{
			echo 'Error: Unknown user';
			return (0);
		}
		if ($result[0]['password'] != hash('whirlpool',$password))
		{
			echo 'Error: Wrong password';
			return (0);
		}
		$update_user = $conn->prepare('UPDATE users SET email=?,password=? WHERE email=?;');
		$update_user->bindParam(1, $newemail);
		$update_user->bindParam(1, $newpassword);
		$update_user->execute();
		echo 'Info: Information changed';
		// echo 'Exit';
		// delete_token($conn, $email);
	}

	function check_token($conn, $email, $token)
	{
		$check_token = $conn->prepare("SELECT id FROM tokens WHERE email=? AND token=?;");
		$check_token->bindParam(1, $email);
		$check_token->bindParam(2, $token);
		$check_token->execute();
		$result = $check_token->fetchAll();
		if (count($result) != 1)
			return (0);
		return (1);
	}

	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagru";

	try {
		// echo 'Info: ';
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// echo "Connected successfully";
		$ids = json_decode(hex2bin($token), true);
		// print_r($ids);
		$email = $ids['email'];
		if ($newpassword == '')
			$newpassword == $password;

		if (($check = valid_logs($newemail, $newpassword)) != null) {
			echo $check;
			return ;
		}
		if (check_token($conn, $email, hex2bin($ids['token'])) == 0)
			echo 'Error: Your token seems broken';
		else
		{
			update_user($conn, $email, $password, $newemail, $newpassword);
		}
		// print_r();

	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on login: " . $e->getMessage();
	}
?>
