<?php
	error_reporting(E_ERROR | E_PARSE);
	function check_token($email, $token)
	{
		$servername = "127.0.0.1";
		$username = "root";
		$password = "";
		$port = "8081";
		$dbname = "camagruDB";
		try {
			$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password, array( PDO::ATTR_PERSISTENT => true));
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// echo "Connected successfully";
			$check_token = $conn->prepare("SELECT id FROM tokens WHERE email=? AND token=?;");
			$check_token->bindParam(1, $email);
			$check_token->bindParam(2, $token);
			$check_token->execute();
			$result = $check_token->fetchAll();
			if (count($result) != 1)
				return (0);
			return (1);
		}
		catch(PDOException $e)
		{
			echo "Connection failed on token: " . $e->getMessage();
		}
		return (0);
	}

	try {
		if (array_key_exists ('ids' , $_POST) == false)
			return ;
		if ($_POST['ids'] == null)
			return;
		$ids = json_decode(hex2bin($_POST['ids']), true);
		if (check_token($ids['email'], hex2bin($ids['token'])) == 0)
			echo 'Not connected';
		else
			echo 'Connected';
	}
	catch(Exception $e) {
		echo 'Not connected';
	}
?>