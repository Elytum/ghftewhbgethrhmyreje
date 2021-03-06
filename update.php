<?php
	if (array_key_exists ('ids' , $_POST) == false || array_key_exists ('password' , $_POST) == false || array_key_exists ('newusername' , $_POST) == false || array_key_exists ('newpassword' , $_POST) == false)
		return ;
	$ids = $_POST['ids'];
	$newusername = $_POST['newusername'];
	$newpassword = $_POST['newpassword'];
	$password = $_POST['password'];

	function valid_logs($conn, $user, $password)
	{
		if ($user == '') {
			return "Warning: Missing username"; 
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
	function update_user($conn, $email, $password, $newusername, $newpassword, $token)
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
		$update_user = $conn->prepare('UPDATE users SET username=?,password=? WHERE email=?;');
		$update_user->bindParam(1, htmlspecialchars($newusername, ENT_QUOTES, 'UTF-8'));
		$update_user->bindParam(2, hash('whirlpool',$newpassword));
		$update_user->bindParam(3, $email);
		$update_user->execute();

		$json = array(
			"email" => $email, 
			"token" => bin2hex($token),
			"username" => $newusername
		);
		echo("OK: ".bin2hex(json_encode($json)));
		// echo 'Info: Information changed 2';
		return (1);
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

		// echo 'Error: ';
	try {
		// echo '0:';
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		// echo '1:';
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// echo '2:';
		$ids = json_decode(hex2bin($ids), true);
		// echo '3:';
		$email = $ids['email'];
		if ($newpassword == null)
			$newpassword = $password;
		// echo '4:';
		if (($check = valid_logs($conn, $newusername, $newpassword)) != null) {
			// echo '5:';
			echo $check;
			return ;
		}

		$username_exist = $conn->prepare('SELECT COUNT(*) FROM users WHERE username=? AND email!=?;');
		$username_exist->bindParam(1, htmlspecialchars($newusername, ENT_QUOTES, 'UTF-8'));
		$username_exist->bindParam(2, $email);
		$username_exist->execute();

		try {
			if ($username_exist->fetchAll()[0][0] != 0) {
				echo 'Error: Username already taken';
				return ;
			}
		}
		catch (Exception $e) {
			;
		}

		$token = hex2bin($ids['token']);
		// echo '6:';
		if (check_token($conn, $email, $token) == 0)
			echo 'Error: Your token seems broken';
		else
		{
		// echo '7:';
			update_user($conn, $email, $password, $newusername, $newpassword, $token);
		// echo '8:';
		}
		// echo '9:';
		// print_r();

	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on login: " . $e->getMessage();
		echo $e->getMessage();
	}
?>
