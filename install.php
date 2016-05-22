<?php
	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagru";

	try {
		$conn = new PDO("mysql:host=$servername;port=$port;", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		echo 'CREATE DATABASE';
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$create_database = $conn->prepare('CREATE DATABASE '.$dbname.';');
		$create_database->execute();

		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$commands = [
						'CREATE TABLE users (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, username VARCHAR(255), email VARCHAR(255) NOT NULL, password TEXT CHARACTER SET ascii NOT NULL, creation_date INT, ready BIT, token VARCHAR(40) );',
						'CREATE TABLE tokens (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, last_request INT );',
						'CREATE TABLE images (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, b64 TEXT CHARACTER SET ascii NOT NULL, author VARCHAR(255) NOT NULL, commentary VARCHAR(255) );',
						'CREATE TABLE comments (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, image INT, author VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL );',
						'CREATE TABLE likes (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, image INT, author VARCHAR(255) NOT NULL );',
						'CREATE TABLE resets (id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL, token VARCHAR(40) NOT NULL );*'
					];
		foreach ($commands as $command) {
			echo $command;
			$execute_command = $conn->prepare($command);
			$execute_command->execute();
		}


	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>
