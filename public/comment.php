<?php
	if (array_key_exists ('ids' , $_POST) == false ||
		array_key_exists ('comment' , $_POST) == false ||
		array_key_exists ('id' , $_POST) == false)
		return ;
	$ids = json_decode(hex2bin($_POST['ids']), true);
	$email = $ids['email'];
	$token = hex2bin($ids['token']);
	$comment = $_POST['comment'];
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
		$add_comment = $conn->prepare('INSERT INTO comments (image, author, content) VALUES (?, ?, ?);');
		$add_comment->bindParam(1, $id);
		$add_comment->bindParam(2, $email);
		$add_comment->bindParam(3, htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'));
		$add_comment->execute();

		$get_author = $conn->prepare('SELECT author from images WHERE id=?;');
		$get_author->bindParam(1, $id);
		$get_author->execute();
		$get_author = $get_author->fetchAll();

		$author = $get_author[0]['author'];
		echo $author;

		if ($author == $email)
			return;
		mail($author,"Picture commented",$email." just commented your picture:\nhttp://127.0.0.1:8080/picture.php?id=".$id);
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>