<?php
	try {
		if (array_key_exists ('id' , $_POST) == false ||
			array_key_exists ('from' , $_POST) == false)
			return ;
		$id = $_POST['id'];
		$from = $_POST['from'];

		$servername = "127.0.0.1";
		$username = "root";
		$pass = "";
		$port = "8081";
		$dbname = "camagru";
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$get_likes = $conn->prepare('SELECT * FROM comments WHERE image=?;');
		$get_likes->bindParam(1, $id);
		$get_likes->execute();
		$result = $get_likes->fetchAll();
		foreach ($result as $key => $value) {
			$get_username = $conn->prepare('SELECT username FROM users WHERE email=?;');
			$get_username->bindParam(1, $value['author']);
			$get_username->execute();
			$get_username = $get_username->fetchAll();
			try {
				$result[$key]['author'] = $get_username[0]['username'];
			}
			catch (Exception $e) {
				;
			}
		}
		echo json_encode(array_slice($result, $from));
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>