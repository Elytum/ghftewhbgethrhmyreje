<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
	<br>

	<style>

	.picture	{
		width: 500px;
		height: 500px;
	}
	.commentaries {
		margin: 20px;
	}
	.commentary	{
		width: 500px;
		margin-left: auto;
		margin-right: auto;
		word-wrap:break-word;
	} 
	.commentary:nth-child(odd) {
		background: #DEDEDE;
	}

	.commentary:nth-child(even) {
		background: #D3D3D3;
	}

	.comment_field {
		width: 425px;
		height: 75px;
	}

	.comment_button {
		width: 75px;
		height: 75px;
	}

	</style>

	<script>

		function hex2bin(hex)
		{
			var bytes = [], str;

			for(var i=0; i< hex.length-1; i+=2)
			bytes.push(parseInt(hex.substr(i, 2), 16));

			return String.fromCharCode.apply(String, bytes);    
		}

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

		function send() {
			comment = document.getElementById("comment").value;
			if (comment == "")
				return ;
			document.getElementById("comment").value = "";
			var xhr = getXMLHttpRequest();

			xhr.open("POST", "comment.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("ids="+localStorage.getItem('ids')+"&id="+<?php echo '"'.$_GET['id'].'"'?>+"&comment="+comment);
		}

		function like() {
			var xhr = getXMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
					if (xhr.responseText != '')
					{
						var fullPath = document.getElementById("like_button").src;
						var filename = fullPath.replace(/^.*[\\\/]/, '');
						if (filename == "like.png")
							document.getElementById("like_button").src = "imgs/liked.png";
						else
							document.getElementById("like_button").src = "imgs/like.png";
					}
				}
			};
			xhr.open("POST", "like.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("ids="+localStorage.getItem('ids')+"&id="+<?php echo '"'.$_GET['id'].'"'?>);
		}

		function setupLike() {
			var xhr = getXMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
					if (xhr.responseText != '')
					{
						console.log(xhr.responseText);
						if (xhr.responseText == 'YES')
							document.getElementById("like_button").src = "imgs/liked.png";
						else
							document.getElementById("like_button").src = "imgs/like.png";
					}
				}
			};
			xhr.open("POST", "do_like.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("ids="+localStorage.getItem('ids')+"&id="+<?php echo '"'.$_GET['id'].'"'?>);
		}

		var likesReady = true;
		var commentsReady = true;

		function update() {
			if (document.getElementById("likes") != null && likesReady == true)
			{
				likesReady = false;
				var requestLikes = getXMLHttpRequest();
				requestLikes.onreadystatechange = function() {
					if (requestLikes.readyState == 4 && requestLikes.status == 200) {
						if (requestLikes.responseText != '')
							document.getElementById("likes").innerHTML = requestLikes.responseText + " like(s)";
					}
					likesReady = true;
				};
				requestLikes.open("POST", "count_likes.php", true);
				requestLikes.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				requestLikes.send("id="+<?php echo '"'.$_GET['id'].'"'?>);
			}
			if (document.getElementById("commentaries") != null && commentsReady == true)
			{
				commentsReady = false;
				var requestComments = getXMLHttpRequest();
				requestComments.onreadystatechange = function() {
					if (requestComments.readyState == 4 && requestComments.status == 200) {
						try {
							var newComments = JSON.parse(requestComments.responseText);
							for (id in newComments) {
								var newDiv = document.createElement("div");
								newDiv.className = "commentary";
								newDiv.setAttribute("name", "commentary");
								newDiv.innerHTML = newComments[id]['author']+'<br>'+newComments[id]['content'];
								document.getElementById("commentaries").appendChild(newDiv);
							}
						}
						catch (e) {
						}
					}
					commentsReady = true;
				};
				requestComments.open("POST", "get_comments.php", true);
				requestComments.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				requestComments.send("id="+<?php echo '"'.$_GET['id'].'"'?>+"&from="+document.getElementsByName("commentary").length.toString());
			}
			setTimeout(update, 1000);
		}

		update();

		window.onload = setupLike;

	</script>


	<?php include('footer.php');?>
	</body>
</html>

<?php
	function put_image($conn, $id)
	{
		$put_image = $conn->prepare("SELECT b64, author, commentary FROM images WHERE id=?;");
		$put_image->bindParam(1, $id);
		$put_image->execute();
		$result = $put_image->fetchAll();
		try {
			$b64 = $result[0]['b64'];
			$get_comments = $conn->prepare('SELECT author, content from comments WHERE image=?;');
			$get_comments->bindParam(1, $id);
			$get_comments->execute();
			$result = $get_comments->fetchAll();

			$get_likes = $conn->prepare('SELECT * FROM likes WHERE image=?;');
			$get_likes->bindParam(1, $id);
			$get_likes->execute();
			$likes = $get_likes->fetchAll();
			echo '<img class="picture" src="data:image/jpeg;charset=utf-8;base64,'.$b64.'">';
			echo '<div id="commentaries" class="commentaries">';
			echo '<div id=likes>'.count($likes).' like(s)'.'</div>';
			foreach ($result as $comment) {
				echo '	<div name="commentary" class="commentary">';
				echo $comment['author'];
				echo '<br>';
				echo $comment['content'];
				echo '</div>';
			}
			echo '</div>';
			echo '<textarea id="comment" class="comment_field" maxlength="255"></textarea>';
			echo '<img onclick="send();" class="comment_button" src="imgs/send.png">';
			echo '<img id="like_button" width=50px height=50px onclick="like();" src="imgs/like.png">';
			echo '<br><br><br><br><br><br><br><br><br><br><br><br>';
		}
		catch(PDOException $e)
		{
			echo "Error: Unknown";//Connection failed on login: " . $e->getMessage();
			// echo $e->getMessage();
		}
	}

	$picture_id = $_GET['id'];

	$servername = "127.0.0.1";
	$username = "root";
	$pass = "";
	$port = "8081";
	$dbname = "camagru";

	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		put_image($conn, $picture_id);
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on login: " . $e->getMessage();
		echo $e->getMessage();
	}
?>
