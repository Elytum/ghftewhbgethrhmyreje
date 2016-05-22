<?php
	if (array_key_exists ('formType' , $_POST) == false ||
		array_key_exists ('email' , $_POST) == false ||
		array_key_exists ('password' , $_POST) == false)
		return ;
	$type = $_POST['formType'];
	$email = $_POST['email'];
	$password = $_POST['password'];

/*CREATE TABLE users (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, username VARCHAR(255), email VARCHAR(255) NOT NULL, password TEXT CHARACTER SET ascii NOT NULL, creation_date INT, ready BIT, token VARCHAR(40) );*/
/*CREATE TABLE tokens (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, last_request INT );*/
/*CREATE TABLE images (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, b64 TEXT CHARACTER SET ascii NOT NULL, author VARCHAR(255) NOT NULL, commentary VARCHAR(255) );*/
/*CREATE TABLE comments (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, image INT, author VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL );*/
/*CREATE TABLE likes (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, image INT, author VARCHAR(255) NOT NULL );*/
/*CREATE TABLE resets (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL, token VARCHAR(40) NOT NULL );*/

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
		date_default_timezone_set('Europe/Paris');
		$token = bin2hex(openssl_random_pseudo_bytes(10));
		$create_user = $conn->prepare('INSERT INTO users (username,email,password,creation_date, ready,token) VALUES (?, ?, ?, ?, 0, ?);');
		$create_user->bindParam(1, explode("@", $email)[0]);
		$create_user->bindParam(2, $email);
		$create_user->bindParam(3, hash('whirlpool',$password));
		$create_user->bindParam(4, date_timestamp_get(date_create()));
		$create_user->bindParam(5, $token);
		$create_user->execute();
		mail($email, "Account validation", "127.0.0.1:8080/validate.php?email=".$email."&token=".$token);
		echo 'Info: Please validate your subscription by mail';
		// connect_user($conn, $email, $password);
	}

	function has_token($conn, $email)
	{
		$has_token = $conn->prepare("SELECT id FROM tokens WHERE email=?;");
		$has_token->bindParam(1, $email);
		$has_token->execute();
		$result = $has_token->fetchAll();
		if (count($result) != 0)
			return ($result[0]['id']);
		return (-1);
	}

	function create_token($conn, $email)
	{
		date_default_timezone_set('Europe/Paris');
		$token = openssl_random_pseudo_bytes(255);
		$id = has_token($conn, $email);
		if ($id != -1)
		{
			$create_token = $conn->prepare('UPDATE tokens SET token=?, last_request=? WHERE id=?');
			$create_token->bindParam(1, $token);
			$create_token->bindParam(2, date_timestamp_get(date_create()));
			$create_token->bindParam(3, $id);
			$create_token->execute();
		}
		else
		{
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
		$connect_user = $conn->prepare('SELECT username, password, ready FROM users WHERE email=?');
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
		if ($result[0]['ready'] == 0)
		{
			echo 'Warning: Please confirm your email';
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
			"token" => bin2hex($token),
			"username" => $result[0]['username']
		);
		echo(bin2hex(json_encode($json)));
	}

	function email_exists($conn, $email)
	{
		$email_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE email=?;");
		$email_check->bindParam(1, $email);
		$email_check->execute();
		try {
			$email_check = $email_check->fetchAll()[0][0];
			return ($email_check == 1);
		}
		catch (Exception $e) {
			return false;
		}
	}

	function forgot_password($conn, $email)
	{
		if ($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Warning: Please provide your email';
			return;
		}
		else if (email_exists($conn, $email) == false) {
			echo 'Warning: Unknown email'; 
			return;
		}
		else {
			$count_resets = $conn->prepare('SELECT COUNT(*) FROM resets WHERE email=?;');
			$count_resets->bindParam(1, $email);
			$count_resets->execute();
			$result = $count_resets->fetchAll();
			if (count($result) >= 1) {
				$remove_resets = $conn->prepare('DELETE FROM resets WHERE email=?;');
				$remove_resets->bindParam(1, $email);
				$remove_resets->execute();
			}
			$token = bin2hex(openssl_random_pseudo_bytes(10));
			$add_reset = $conn->prepare('INSERT INTO resets (email, token) VALUES (?, ?);');
			$add_reset->bindParam(1, $email);
			$add_reset->bindParam(2, $token);
			$add_reset->execute();
		}
		echo 'Info: A password reset has been sent to your email.';
		// mail($email, "Password reset requested", "http://127.0.0.1:8080/doreset.php?email=".$email."&token=".$token);
		mail($email, "Password reset requested", "127.0.0.1:8080/doreset.php?email=".$email."&token=".$token);
		// mail($email, "Password reset requested", "token=".$token);
		// mail($email, "Subject", "Content");
	}

			// echo 'xd';
	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagru";
	if ($type == 'subscribe')
	{
		if (($check = valid_logs($email, $password)) != null) {
			echo $check;
			return ;
		}

		try {
			$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// echo "Connected successfully";
			create_user($conn, $email, $password);
		}
		catch(PDOException $e)
		{
			echo "Error: Unknown";//Connection failed on subscribe: " . 
			echo $e->getMessage();
		}
	}
	else if ($type == 'login')
	{
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
	else if ($type == 'forgot')
	{
		try {
			$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// echo "Connected successfully";
			forgot_password($conn, $email);
		}
		catch(PDOException $e)
		{
			echo "Error: Unknown";//Connection failed on login: " . 
			echo $e->getMessage();
		}
	}
?>
