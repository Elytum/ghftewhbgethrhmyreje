<?php
	if (array_key_exists ('email' , $_GET) == false || array_key_exists ('token' , $_GET) == false)
		return ;
	$email = $_GET['email'];
	$token = $_GET['token'];
	echo $email;
	echo $token;
?>
