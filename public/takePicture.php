<?php

	$rootPics = getcwd().'/pics';
	$addonPath = realpath($rootPics.'/'.$_POST['addon']);

	$ids = $_POST['ids'];
	echo 'BEGIN'.$ids.'END';
	// echo hex2bin($ids);
	// echo json_decode($_POST['ids']);

	$ids = json_decode(hex2bin($_POST['ids']), true);
	$email = $ids['email'];
	$token = hex2bin($ids['token']);

	if (startsWith($addonPath, $rootPics))
		echo 'Valid';
	else {
		echo 'ERROR';
		return ;
	}

	function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}

	function base64_to_jpeg($base64_string, $output_file) {
		$ifp = fopen($output_file, "wb"); 

		$data = explode(',', $base64_string);

		fwrite($ifp, base64_decode($data[1])); 
		fclose($ifp); 

		return $output_file; 
	}
	$data = explode(',', $_POST['img']);
	$data = base64_decode($data[1]);

	if ($data == '')
		return ;
	$userImage = imagecreatefromstring($data);
	$userImage = imagescale($userImage, 500, 375);

	$addonImage = @imagecreatefrompng($addonPath);
	if ($addonImage === false) {
		$addonImage = @imagecreatefromgif($addonPath);
		if ($addonImage === false) {
			$addonImage = @imagecreatefromjpeg($addonPath);
			if ($addonImage === false) {
				echo 'ERROR';
				return ;
			}
		}
	}
	$addonImage = imagescale($addonImage, 500, 375);
	imagecopy($userImage, $addonImage, 0, 0, 0, 0, 500, 375);

	ob_start();
	imagejpeg($userImage);
	$contents = ob_get_contents();
	imagedestroy($userImage);
	ob_end_clean();

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
	$dbname = "camagruDB";

	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (check_token($conn, $email, $token) == 0)
			return ;
		// echo "Connected successfully";
		$create_token = $conn->prepare('INSERT INTO images (b64, author, commentary) VALUES (?, ?, "");');
		$create_token->bindParam(1, base64_encode($contents));
		$create_token->bindParam(2, $email);
		$create_token->execute();
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>