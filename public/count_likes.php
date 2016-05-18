<?php
	if (array_key_exists ('id' , $_POST) == false)
		return ;
	$id = $_POST['id'];

	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagruDB";

	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$get_likes = $conn->prepare('SELECT * FROM likes WHERE image=?;');
		$get_likes->bindParam(1, $id);
		$get_likes->execute();
		$result = $get_likes->fetchAll();
		echo count($result);
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>