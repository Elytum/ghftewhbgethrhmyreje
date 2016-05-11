<?php
	$type = $_POST['formType'];
	$email = $_POST['email'];
	$password = $_POST['password'];

/*CREATE TABLE users (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, username VARCHAR(255), email VARCHAR(255), password VARCHAR(255), creation_date INT );*/
/*CREATE TABLE tokens (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, email VARCHAR(255), token VARCHAR(255), last_request INT );*/

	function user_exist($conn, $email)
	{
		$user_exist = $conn->prepare("SELECT email FROM users WHERE email=?;");
		$user_exist->bindParam(1, $email);
		$user_exist->execute();
		$result = $user_exist->fetchAll();
		if (count($result) >= 1)
			return (1);
		return (0);
	}

	function create_user($conn, $email, $password)
	{
		if (user_exist($conn, $email))
		{
			echo "Error: User already exist";
			return (0);
		}
		date_default_timezone_set();
		$create_user = $conn->prepare('INSERT INTO users (username,email,password,creation_date) VALUES ("", ?, ?, ?);');
		$create_user->bindParam(1, $email);
		$create_user->bindParam(2, hash('whirlpool',$password));
		$create_user->bindParam(3, date_timestamp_get(date_create()));
		$create_user->execute();
		connect_user($conn, $email, $password);
	}

	function has_token($conn, $email)
	{
		$has_token = $conn->prepare("SELECT id FROM tokens WHERE email=?;");
		$has_token->bindParam(1, $email);
		$has_token->execute();
		$result = $has_token->fetchAll();
		if (count($result) != 0)
			return ($result['id']);
		return (-1);
	}

	function create_token($conn, $email)
	{
		date_default_timezone_set();
		$token = openssl_random_pseudo_bytes(255);
		if ($id = has_token($conn, $email) != -1)
		{
			// echo 'Already have token';
			$create_token = $conn->prepare('UPDATE tokens SET token=?, last_request=? WHERE id=?');
			$create_token->bindParam(1, $token);
			$create_token->bindParam(2, date_timestamp_get(date_create()));
			$create_token->bindParam(3, $id);
			$create_token->execute();
		}
		else
		{
			// echo 'No token actually';
			$create_token = $conn->prepare('INSERT INTO tokens (email,token,last_request) VALUES (?, ?, ?);');
			$create_token->bindParam(1, $email);
			$create_token->bindParam(2, $token);
			$create_token->bindParam(3, date_timestamp_get(date_create()));
			$create_token->execute();
		}
		return ($token);
	}

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

	function connect_user($conn, $email, $password)
	{
		// $connect_user = $conn->prepare('SELECT email FROM users WHERE email=? AND password=?;');
		$connect_user = $conn->prepare('SELECT password FROM users WHERE email=?');
		$connect_user->bindParam(1, $email);
		// $connect_user->bindParam(2, hash('whirlpool',$password));
		$connect_user->execute();
		$result = $connect_user->fetchAll();
		// echo 'Error: ';
		// echo $email;
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
		$token = create_token($conn, $email);
		$json = array(
			"email" => $email, 
			"token" => bin2hex($token)
		);
		echo(bin2hex(json_encode($json)));
	}

			// echo 'xd';
	if ($type == 'subscribe')
	{
		if (($check = valid_logs($email, $password)) != null) {
			echo $check;
			return ;
		}
		$servername = "127.0.0.1";
		$username = "root";
		$pass = "";
		$port = "8081";
		$dbname = "camagru";

		try {
			$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// echo "Connected successfully";
			create_user($conn, $email, $password);
		}
		catch(PDOException $e)
		{
			echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
		}
	}
	else if ($type == 'login')
	{
		$servername = "127.0.0.1";
		$username = "root";
		$pass = "";
		$port = "8081";
		$dbname = "camagru";

		try {
			$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// echo "Connected successfully";
			connect_user($conn, $email, $password);
		}
		catch(PDOException $e)
		{
			echo "Error: Unknown";//Connection failed on login: " . $e->getMessage();
		}
	}
?>
