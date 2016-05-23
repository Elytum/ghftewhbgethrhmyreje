<html>
	<head>
	</head>
	<body>
		<form action="call_reset.php" method="get">
			New password:
			<input type="password" name="newpassword" value="" class="formular" />
			<input hidden="true" type="text" name="email" value=<?php if (array_key_exists ('email' , $_GET) == false) echo "\"\""; else echo "\"".$_GET['email']."\""?> class="formular" />
			<input hidden="true" type="text" name="token" value=<?php if (array_key_exists ('token' , $_GET) == false) echo "\"\""; else echo "\"".$_GET['token']."\""?> class="formular" />
			<input name="submit" type="submit" value="Submit">
		</form>
	</body>
</html>