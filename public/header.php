<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title>Camagru</title>
<link rel="stylesheet" type="text/css" href="header.css">
<link rel="stylesheet" type="text/css" href="style.css">
<ul id="header" class="header">
  <li class="header"><a id="account.php" href="account.php">Mon compte</a></li>
  <li class="header"><a id="montage.php" href="montage.php">Montage</a></li>
  <li class="header"><a id="galerie.php" href="galerie.php">Galerie</a></li>
  <li class="header"><a onclick="disconnect()" href="index.php">Deconnection</a></li>
</ul class="header">

<script>
	function disconnect() {
		localStorage.removeItem('ids');
	}

	function basename(path) {
		return path.split(/[\\/]/).pop();
	}

	function getPage() {
		return basename(window.location.href)
	}

	function highlight() {
		page = document.getElementById(getPage());
		if (page != undefined)
			page.className = "active";
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

	function checkConnection() {
		// Get saved data from localStorage
		var data = localStorage.getItem('ids');
		var xhr = getXMLHttpRequest();

		xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
            	if (xhr.responseText == 'Not connected')
            		localStorage.removeItem('ids');
            	if (getPage() != 'index.php')
            	{
            		if (xhr.responseText == 'Not connected')
	            		window.location = "index.php";
            	}
            	else if (xhr.responseText == 'Connected')
            		window.location = "galerie.php";
            }
        };
		xhr.open("POST", "connexion.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("ids="+data);
	}

	checkConnection();
	window.onload = highlight();
</script>
