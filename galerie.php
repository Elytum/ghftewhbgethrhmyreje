<html>
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

#nav {
    text-align: justify;
    width: 50%;
	margin: auto;
    /*min-width: 500px;*/
}
#nav:after {
    content: '';
    display: inline-block;
    width: 100%;
}
#nav li {
    display: inline-block;
}
	</style>

<?php
	function list_images($conn, $page)
	{
		$nb_pages = 5;

		$pages_count = $conn->prepare("SELECT COUNT(*) FROM images;");
		$pages_count->bindParam(1, $id);
		$pages_count->execute();
		try {
			$pages_count = ceil($pages_count->fetchAll()[0][0] / 10);
		}
		catch (Exception $e) {
			$pages_count = 0;
		}
		if ($page < 1 && $pages_count != 0) {
			header('Location: galerie.php');
			exit();
		}
		else if ($page > $pages_count) {
			header('Location: galerie.php?page='.$pages_count);
			exit();
		}
		echo '<head>';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		echo '<meta charset="UTF-8">';
		echo '<title>Camagru</title>';
		echo '<link rel="stylesheet" type="text/css" href="header.css">';
		echo '<link rel="stylesheet" type="text/css" href="style.css">';
		echo '<ul id="header" class="header">';
		echo '	<li class="header"><a id="account.php" href="account.php">Mon compte</a></li>';
		echo '	<li class="header"><a id="montage.php" href="montage.php">Montage</a></li>';
		echo '	<li class="header"><a id="galerie.php" href="galerie.php">Galerie</a></li>';
		echo '	<li class="header"><a onclick="disconnect()" href="index.php">Deconnection</a></li>';
		echo '</ul class="header">';
		echo '</head>';
		$first = $page - intval($nb_pages / 2);
		$last = $page + intval($nb_pages / 2);
		if ($first < 1) {
			$last += -$first + 1;
			$first = 1;
		}
		if ($last > $pages_count) {
			$first += $pages_count - $last;
			$last = $pages_count;
		}
		if ($first < 1)
			$first = 1;

		echo '<ul id="nav">';
		while ($first <= $last) {
			echo '<li><a href="galerie.php?page='.$first.'">'.$first.'</a></li>
';
			$first += 1;
		}
		echo '</ul>';

		echo '<div class="galerie">';
		echo '<div class="content" id="list">';
		$page = ($page - 1) * 10;
		$list_images = $conn->prepare("SELECT id, b64, author, commentary FROM images LIMIT ".strval(intval($page)).", 10;");
		$list_images->execute();
		$result = $list_images->fetchAll();
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


				$get_like = $conn->prepare('SELECT * FROM likes WHERE image=? AND author=?;');
				$get_like->bindParam(1, $id);
				$get_like->bindParam(2, $email);
				$get_like->execute();
				$result = $get_like->fetchAll();
				if (count($result) >= 1)
					$img = 'imgs/liked.png';
				else
					$img = 'imgs/like.png';

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
									<img id="'.$id.'" onclick="like('.$id.')" class="likebutton" src="'.$img.'">
								</div>
							</div>
						</span>';
			}
			catch (Exception $e) {
			}
		}
		echo '<span><div class="left"></div><div class="right"></div></span>';
		echo '</div>';
		echo '</div>';
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

<script>

	function getXMLHttpRequest() {
		var xhr = null;

		if (window.XMLHttpRequest || window.ActiveXObject) {
			if (window.ActiveXObject) {
				try {
					xhr = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e) {
					xhr = new ActiveXObject("Microsoft.XMLHTTP");
				}
			}
			else {
				xhr = new XMLHttpRequest(); 
			}
		} else {
			alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
			return null;
		}
		return xhr;
	}

	var ignore = false;
	var can_like = false;

	function clicked(id)
	{
		if (ignore == false)
			window.location = "picture.php?id="+id.toString();
		else
			ignore = false;
	}

	function like(id) {
		if (localStorage.getItem('ids') == null)
			return;
		ignore = true;
		if (can_like == false)
			return ;
		var xhr = getXMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				if (xhr.responseText != '')
				{
					var fullPath = document.getElementById(id).src;
					var filename = fullPath.replace(/^.*[\\\/]/, '');
					if (filename == "like.png")
						document.getElementById(id).src = "imgs/liked.png";
					else
						document.getElementById(id).src = "imgs/like.png";
				}
			}
		};
		xhr.open("POST", "like.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("ids="+localStorage.getItem('ids')+"&id="+id);
	}

	function update_like(id) {
		if (localStorage.getItem('ids') == null)
			return;
		var xhr2 = getXMLHttpRequest();
		xhr2.onreadystatechange = function() {
			if (xhr2.readyState == 4 && xhr2.status == 200) {
				if (xhr2.responseText != '')
				{
					if (xhr2.responseText == 'YES')
						document.getElementById(id).src = "imgs/liked.png";
					else
						document.getElementById(id).src = "imgs/like.png";
				}
			}
		};
		xhr2.open("POST", "do_like.php", true);
		xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr2.send("ids="+localStorage.getItem('ids')+"&id="+id);
	}

	function update_likes() {
		var elements = document.getElementsByClassName("likebutton");
		for (element in elements) {
			id = elements[element]['id'];
			if (id == undefined)
				continue;
			update_like(id);
		}
		can_like = true;
	}

	window.onload = update_likes();
</script>
	<br><br><br><br><br><br>
	<?php include('footer.php');?>
	</body>
</html>