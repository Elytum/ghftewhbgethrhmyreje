<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
	<script>
		(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
		fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>

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
		background-color: #666;
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

<?php
	function list_images($conn)
	{
		echo '<div class="galerie">';
		echo '<div class="content" id="list">';
		$list_images = $conn->prepare("SELECT id, b64, author, commentary FROM images;");
		$list_images->execute();
		$result = $list_images->fetchAll();
		$counter = 0;
		foreach ($result as $value) {
			if ($value['author'] == null)
				continue ;
			echo	'<span class="element" onclick="console.log(\''.$value['id'].'\')">
						<div class="left">
							<img class="picture" src="data:image/jpeg;charset=utf-8;base64,'.$value['b64'].'">
						</div>
						<div class="right">
							<div class="author">
								'.$value['author'].'
							</div>
							<div class="comment">
								'.$value['commentary'].'
							</div>
							<div class="stats">
								<div class="review">
									<br>X comments Y likes
								</div>
								<img class="likebutton" src="like.png">
							</div>
						</div>
					</span>';
		}
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

	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $pass, array( PDO::ATTR_PERSISTENT => true));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		list_images($conn);
		// echo "Connected successfully";
		$create_token = $conn->prepare('INSERT INTO images (b64, author, commentary) VALUES (?, ?, "");');
		$create_token->bindParam(1, $contents);
		$create_token->bindParam(2, $email);
		$create_token->execute();
	}
	catch(PDOException $e)
	{
		echo "Error: Unknown";//Connection failed on subscribe: " . $e->getMessage();
	}
?>

<script>
		// function addPicture(picture, addon, author, comment, review) {
		// 	var ul = document.getElementById("list");
		// 	var li = document.createElement("li");
		// 	li.setAttribute("class", "galerie");
		// 	var div = document.createElement("div");
		// 	div.setAttribute("class", "container");
		// 	var pic = document.createElement("img");
		// 	pic.setAttribute("width", "200px");
		// 	pic.setAttribute("height", "200px");
		// 	pic.setAttribute("class", "picture");
		// 	pic.setAttribute("src", picture);
		// 	var add = document.createElement("img");
		// 	add.setAttribute("width", "200px");
		// 	add.setAttribute("height", "200px");
		// 	add.setAttribute("class", "addon");
		// 	add.setAttribute("src", addon);
		// 	var aut = document.createElement("h3");
		// 	aut.innerHTML = author;
		// 	var com = document.createElement("p");
		// 	com.innerHTML = comment;
		// 	com.setAttribute("class", "text-field");
		// 	rev = document.createElement("div");
		// 	rev.setAttribute("class", "review");
		// 	rev.innerHTML = review;
		// 	div.appendChild(pic);
		// 	div.appendChild(add);
		// 	div.appendChild(aut);
		// 	div.appendChild(com);
		// 	div.appendChild(rev);
		// 	li.appendChild(div);
		// 	ul.appendChild(li);
		// }
</script>

	<!-- <div class="fb-page" 
		data-href="https://www.facebook.com/GameOfThrones"
		data-small-header="true"
		data-hide-cover="false"
		data-show-facepile="true"
		data-show-posts="false">
	</div> -->
	<br><br><br><br><br><br>
	<?php include('footer.php');?>
	</body>
</html>