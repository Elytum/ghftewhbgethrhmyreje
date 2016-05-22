<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>

<!-- <input type="file" accept="image/*" onchange="loadFile(event)"> -->
<input type="file" accept="image/*" onchange="loadFile()"><br>
<img id="output"/>
<script>
  var loadFile = function() {
	var file	= document.querySelector('input[type=file]').files[0];
	var reader	= new FileReader();

	reader.addEventListener("load", function () {
		if (document.getElementById('undefined') == null)
		{
			var video = document.getElementById('videoElement');
			video.pause();
			video.id = 'undefined';
			video.style.display = 'none';
			var picture = document.getElementById('pictureElement');
			picture.id = 'videoElement';
			picture.style.display = 'block';
		}
		else 
			var picture = document.getElementById('videoElement');
		picture.src = reader.result;
		// var send_raw = getXMLHttpRequest();
		// console.log(reader.result);
		// send_raw.open("POST", "takePicture.php", true);
		// send_raw.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		// send_raw.send('ids='+localStorage.getItem('ids')+'&img='+reader.result+'&addon='+document.getElementById("cornerimage").src.replace(/^.*[\\\/]/, ''));
	}, false);

	if (file) {
		reader.readAsDataURL(file);
	}
  };
</script>

	<br>
	<?php include('footer.php');?>

	<style>
		#container {
			margin: 0px auto;
			width: 500px;
			height: 375px;
			border: 10px #333 solid;
			position:relative;
		}
		#videoElement {
			width: 500px;
			height: 375px;
			background-color: #666;
		}
		#cornerimage {
			position: absolute;
			top: 47;
			left: 0;
			height:281px;
		}
	</style>

	<div id="container">
	    <video autoplay="true" id="videoElement">
	    </video>
	    <img style="display:none;" autoplay="true" id="pictureElement">
	   	<img width="500" height="375" src="" id="cornerimage"/>
	   	<!-- <img width="500" height="375" src="moving_cat.gif" id="cornerimage"/> -->
	</div>

	<canvas hidden="true" id="drawCanvas" height="400" width="400"> </canvas>
	<button onclick="savePicture()">Click me</button>

	<script>

		// Video taking

		var video = document.querySelector("#videoElement");

		navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

		if (navigator.getUserMedia) {
			var constraints = {
				audio: false,
				video: {
					optional: [
						{minWidth: 320},
						{minWidth: 640},
						{minWidth: 800},
						{minWidth: 900},
						{minWidth: 1024},
						{minWidth: 1280},
						{minWidth: 1920},
						{minWidth: 2560}
						]
					}
				};       
			navigator.getUserMedia(constraints, handleVideo, videoError);
		}

		function handleVideo(stream) {
			video.src = window.URL.createObjectURL(stream);
			var image = document.getElementById('cornerimage');
		}

		function videoError(e) {
			// do something
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

		function savePicture() {
			var drawCanvas = document.getElementById('drawCanvas');
			if (document.getElementById('undefined') == null)
			{
				var video = document.getElementById('videoElement');
				drawCanvas.height = video.videoHeight;
				drawCanvas.width = video.videoWidth;

				draw(video, drawCanvas);
			}
			else {
				var context = drawCanvas.getContext('2d');
				context.drawImage(document.getElementById('videoElement'), 0, 0, 400, 400);
			}
			var base64 = drawCanvas.toDataURL();

			var xhr = getXMLHttpRequest();
			xhr.open("POST", "takePicture.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send('ids='+localStorage.getItem('ids')+'&img='+encodeURIComponent(base64)+'&addon='+document.getElementById("cornerimage").src.replace(/^.*[\\\/]/, ''));
		}

		function draw(video, drawCanvas) {

			// get the canvas context for drawing
			var context = drawCanvas.getContext('2d');

			// draw the video contents into the canvas x, y, width, height
			context.drawImage(video, 0, 0, drawCanvas.width, drawCanvas.height);

			// get the image data from the canvas object
			var dataURL = drawCanvas.toDataURL("image/jpeg");

		}

		function changeAddon(name) {
			document.getElementById("cornerimage").src=name;
		}

	</script>
	<style type="text/css">
		#images {
			padding: 5px;
			overflow: auto;
			position: relative;
			width:900px;
			margin:20px auto;
			white-space: nowrap;
		}
		#commentaire {
			position: relative;
			width:910px;
			color:white;
			margin:auto;
		}

		.image {
			display: inline-block;
			width: 300px;
			height: 200px;
			border: 1px solid black;
			text-align: center;
			line-height: 400px;
		}
	</style>
</head>
<body>
	<?php
		$dir    = './pics';
		$pics = scandir($dir);
		$first = false;

		echo '<div id="images">';
		foreach ($pics as $value) {
			if ($value[0] != '.')
			{
				$img = $dir.'/'.$value;
				if ($first === false) {
					$first = true;
					echo '<script>changeAddon(\''.$img.'\');</script>';
				}
				echo '<div onclick="changeAddon(\''.$img.'\')" style="background-image: url(\''.$img.'\');background-size: 100% 100%;" class="image"></div>';
			}
		}
		echo '</div>';
	?>
	</body>
</html>