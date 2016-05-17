<?php
	$ids = json_decode(hex2bin($_POST['ids']), true);
	$email = $ids['email'];
	$token = hex2bin($ids['token']);
	$id = $_POST['id'];

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
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (check_token($conn, $email, $token) == 0)
			return ;
		// echo "Connected successfully";
		$get_like = $conn->prepare('SELECT * FROM likes WHERE image=? AND author=?;');
		$get_like->bindParam(1, $id);
		$get_like->bindParam(2, $email);
		$get_like->execute();
		$result = $get_like->fetchAll();
		if (count($result) >= 1)
			echo "YES";
		else
			echo "NO";
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>