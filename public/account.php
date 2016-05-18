<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
	<br>
	<div id="response" style="display:none" class="info">Info message</div>
	<form id="identification" method="post" action="identification.php">
		<div class="accountbox">
			<h1>Account settings</h1>

			<h1>New username:</h1>
			<input id="newusername" type="text" name="newusername" value="" class="formular" />
			<br>
			<h1>New password:</h1>
			<input id="newpassword" type="password" name="" value="" class="formular" />
			<br>
			<h1>Old password:</h1>
			<input id="password" type="password" name="" value="" class="formular" />
			<input hidden="true" name="formType" id="formType" />
			
			<a onclick="handleClick('update');" ><div class="accountbtn">Change my settings</div></a>
			<a onclick="handleClick('delete');" ><div class="deletebtn">DELETE MY ACCOUNT</div></a>

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

				function handleClick(type) {
					document.getElementById('formType').value = type;
					document.getElementById('identification').submit();
				}

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

				document.addEventListener("DOMContentLoaded", function(event) {
					document.getElementById('newusername').value = JSON.parse(hex2bin(localStorage.getItem('ids')))['username'];
				})

				function handleClick(type) {
					if (type == 'delete')
					{
						var confirmation = window.prompt("Are you sure ? Confirm your password", "");
						if (confirmation == '')
							return ;
						var url = "delete.php";
						var data = 'ids='+localStorage.getItem('ids')+'&password='+confirmation;
					}
					else if (type == 'update')
					{
						var url = "update.php";
						var data = 'ids='+localStorage.getItem('ids')+'&newusername='+document.getElementById('newusername').value+'&newpassword='+document.getElementById('newpassword').value+'&password='+document.getElementById('password').value;
					}
					else
						return ;
					var xhr = getXMLHttpRequest();

					xhr.onreadystatechange = function() {
						if (xhr.readyState == 4 && xhr.status == 200) {
							if (xhr.responseText != '')
							{
								if (xhr.responseText == ("Exit"))
								{
									localStorage.removeItem('ids');
									window.location = "index.php";
								}
								else if (xhr.responseText.startsWith("Error: "))
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
								else if (xhr.responseText.startsWith("OK: "))
									localStorage.setItem('ids', xhr.responseText.substring(4));
							}
						}
					};
					xhr.open("POST", url, true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xhr.send(data);
				}
			</script>		  
		</div>
	  
	</form>


	<?php include('footer.php');?>
	<?php mail("camagruachazal@gmail.com","Subject","Message"); ?>
	</body>
</html>
