<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
	<br>

	<div id="response" style="display:none" class="info">Info message</div>

	<script>
		function field_focus(field, email) {
			if (field.value == email) {
				field.value = '';
			}
		}

		function field_blur(field, email) {
			if (field.value == '') {
				field.value = email;
			}
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

		function handleClick(type) {
			var username = document.getElementById('username').value;
			var password = document.getElementById('password').value;
			if (type == 'subscribe') {
				var email = window.prompt("What is your email ?", "");
				if (email == '' || email == null) {
					return ;
				}
				var dataSent = "formType="+type+"&email="+email+"&password="+password+"&username="+username;
			}
			else if (type == 'forgot') {
				var email = window.prompt("What is your email ?", "");
				if (email == '' || email == null) {
					return ;
				}
				var dataSent = "formType="+type+"&email="+email;
			}
			else if (type == 'login') {
				var dataSent = "formType="+type+"&password="+password+"&username="+username;
			}
			var data = localStorage.getItem('ids');
			var xhr = getXMLHttpRequest();

			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && xhr.status == 200) {
					if (xhr.responseText != '')
					{
						if (xhr.responseText.startsWith("Error: "))
						{
							document.getElementById("response").style.display = 'block';
							document.getElementById("response").innerHTML = xhr.responseText;
							document.getElementById("response").className = 'error';
						}
						else if (xhr.responseText.startsWith("Warning: "))
						{
							document.getElementById("response").style.display = 'block';
							document.getElementById("response").innerHTML = xhr.responseText;
							document.getElementById("response").className = 'warning';
						}
						else if (xhr.responseText.startsWith("Info: "))
						{
							document.getElementById("response").style.display = 'block';
							document.getElementById("response").innerHTML = xhr.responseText;
							document.getElementById("response").className = 'info';
						}
						else
						{
							localStorage.setItem('ids', xhr.responseText);
							window.location = "montage.php";
						}
					}
				}
			};
			xhr.open("POST", "identification.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send(dataSent);
		}
		if (localStorage.getItem('ids') == null)
			document.getElementById("header").outerHTML='';
	</script>

	<form id="identification" method="post" action="identification.php">
		<div class="box">
			<h1>Identification</h1>
			<input hidden="true" type="email" name="email" value="email@email.com" onFocus="field_focus(this, 'email@email.com');" onblur="field_blur(this, 'email@email.com');" class="formular" />
			<input id="username" type="text" name="text" value="username" onFocus="field_focus(this, 'username');" onblur="field_blur(this, 'username');" class="formular" />
			<input id="password" type="password" name="password" value="password" onFocus="field_focus(this, 'password');" onblur="field_blur(this, 'password');" class="formular" />
			<input hidden="true" name="formType" id="formType" />
			
			<a onclick="handleClick('login');" ><div class="btn">Sign In</div></a>
			<a onclick="handleClick('subscribe');" ><div id="btn2">Sign Up</div></a>
			<a onclick="handleClick('forgot');" ><div class="btn">Forgot password</div></a>

		</div>
	  
	</form>


	<?php include('footer.php');?>
	</body>
</html>