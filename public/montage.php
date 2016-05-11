<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
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
	   	<img width="500" height="375" src="cat.gif" id="cornerimage"/>
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
			console.log(video);
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
			var video = document.getElementById('videoElement');
			var drawCanvas = document.getElementById('drawCanvas');
			drawCanvas.height = video.videoHeight;
			drawCanvas.width = video.videoWidth;

			draw(video, drawCanvas);
			var base64 = drawCanvas.toDataURL();

			var xhr = getXMLHttpRequest();
			xhr.open("POST", "takePicture.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("img="+encodeURIComponent(base64));
		}

		function draw(video, drawCanvas){

			// get the canvas context for drawing
			var context = drawCanvas.getContext('2d');

			// draw the video contents into the canvas x, y, width, height
			context.drawImage( video, 0, 0, drawCanvas.width, drawCanvas.height);

			// draw the image contents into the canvas x, y, width, height
			// context.drawImage( document.getElementById('cornerimage'), 0, 0, drawCanvas.width, drawCanvas.height);

			// get the image data from the canvas object
			var dataURL = drawCanvas.toDataURL();

		}

	</script>

	</body>
</html>