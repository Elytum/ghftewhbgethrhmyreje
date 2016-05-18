<?php
	if (array_key_exists ('email' , $_GET) == false || array_key_exists ('token' , $_GET) == false)
		return ;
	$email = $_GET['email'];
	$token = $_GET['token'];
	
	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagruDB";
	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$account_exist = $conn->prepare('SELECT COUNT(*) FROM users WHERE email=? AND ready=0;');
		$account_exist->bindParam(1, $email);
		$account_exist->execute();
		$result = $account_exist->fetchAll();
		if ($result[0][0] == 0)
			return ;

		$ready_user = $conn->prepare('UPDATE users SET ready=1 WHERE email=?;');
		$ready_user->bindParam(1, $email);
		$ready_user->execute();
		header('Location: /index.php');
		exit();
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>
