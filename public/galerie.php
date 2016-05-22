<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
	<style>

	/*.fb-page {
		position: relative;
		display: block;
	}*/

	.galerie {
		margin: 20px;
	}

	.content	{
		width: 750px;
		height: 250px;
		background: gray;
		margin-left: auto;
		margin-right: auto;
		padding:0px;
		list-style-type: none;
		line-height: 1.4;
		padding-left: 0;
	} 

	.left, .right	{
		height: 250px;
		position: relative;
	} 

	.left	{
		width: 250px;
		float: left;
	}

	.right	{
		width: 500px;
		float: right;
		word-wrap:break-word;
	}

	.element	{
		background: #2488cb;
		float: left;
		margin: 0 0 0 0;
	}

	.element:hover	{
		background: #2488ee;
		cursor: pointer;
	}

	.picture	{
		width: 250px;
		height: 250px;
		/*background-color: #666;*/
	}

	/*.addon	{
		width: 250px;
		height: 250px;
		position: absolute;
		top: 0;
		left: 0;
	}*/

	.author	{
		/*background: red;*/
		height:50;
	}

	.comment	{
		/*background: green;*/
		height:150;
	}

	.stats	{
		/*background: blue;*/
		height: 50px;
		width: 500px;
	}

	.review, .likebutton	{
		height: 50px;
		position: relative;
	}

	.review	{
		/*background: green;*/
		float: left;
		width: 450px;
	}

	.likebutton	{
		/*background: yellow;*/
		float: right;
		width: 50px;
	}

	</style>

<script>
	var ignore = false;

	function clicked(id)
	{
		if (ignore == false)
			window.location = "picture.php?id="+id.toString();
		else
			ignore = true;
	}

	function like(id) {
		ignore = true;
		console.log("Like on "+id);
	}
</script>

<?php
	function list_images($conn, $page)
	{
		echo '<div class="galerie">';
		echo '<div class="content" id="list">';
		$list_images = $conn->prepare("SELECT id, b64, author, commentary FROM images WHERE id >= ? LIMIT 10;");
		$page = ($page - 1) * 10;
		$list_images->bindParam(1, $page);
		$list_images->execute();
		$result = $list_images->fetchAll();
		$counter = 0;
		foreach ($result as $value) {
			try {
				$id = $value['id'];
				$likes_count = $conn->prepare("SELECT COUNT(*) FROM likes WHERE image=?;");
				$likes_count->bindParam(1, $id);
				$likes_count->execute();
				try {
					$likes_count = $likes_count->fetchAll()[0][0];
				}
				catch (Exception $e) {
					$likes_count = 0;
				}
				$comments_count = $conn->prepare("SELECT COUNT(*) FROM comments WHERE image=?;");
				$comments_count->bindParam(1, $id);
				$comments_count->execute();
				try {
					$comments_count = $comments_count->fetchAll()[0][0];
				}
				catch (Exception $e) {
					$comments_count = 0;
				}

				$get_user = $conn->prepare("SELECT username FROM users WHERE email=?;");
				$get_user->bindParam(1, $value['author']);
				$get_user->execute();
				$user = $get_user->fetchAll()[0]['username'];
				if ($value['author'] == null)
					continue ;
				echo	'<span class="element" onclick="clicked(\''.$id.'\')">
							<div class="left">
								<img class="picture" src="data:image/jpeg;charset=utf-8;base64,'.$value['b64'].'">
							</div>
							<div class="right">
								<div class="author">
									'.$user.'
								</div>
								<div class="comment">
									'.$value['commentary'].'
								</div>
								<div class="stats">
									<div class="review">
										<br>'.strval($comments_count).' comment(s), '.strval($likes_count).' like(s)
									</div>
									<img onclick="like('.$id.')" class="likebutton" src="imgs/like.png">
								</div>
							</div>
						</span>';
			}
			catch (Exception $e) {
			}
		}
		echo '<span class="element"><div class="left"></div><div class="right"></div></span>';
		echo '</div>';
		echo '</div>';
		$counter = $counter + 1;
		if ($counter == 10)
			return ;
	}

	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagru";

	if (array_key_exists ('page' , $_GET))
		$page = $_GET['page'];
	else
		$page = 1;

	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		list_images($conn, $page);
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>

	<br><br><br><br><br><br>
	<?php include('footer.php');?>
	</body>
</html>