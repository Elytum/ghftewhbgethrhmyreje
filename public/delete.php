<?php
	$token = $_POST['token'];
	$password = $_POST['password'];

	function delete_token($conn, $email)
	{
		$delete_token = $conn->prepare('DELETE FROM tokens WHERE email=?');
		$delete_token->bindParam(1, $email);
		$delete_token->execute();
	}

	function delete_user($conn, $email, $password)
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
		$delete_user = $conn->prepare('DELETE FROM users WHERE email=?;');
		$delete_user->bindParam(1, $email);
		$delete_user->execute();
		echo 'Exit';
		delete_token($conn, $email);
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
		$token = hex2bin($ids['token']);
		// echo $email;
		// echo $token;
		// $email = $data['email'];
		// $token = $data['token'];
		if (check_token($conn, $email, $token) == 0)
			echo 'Error: Your token seems broken';
		else
		{
			delete_user($conn, $email, $password);
		}
		// print_r();

	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on login: " . $e->getMessage();
	}
?>
