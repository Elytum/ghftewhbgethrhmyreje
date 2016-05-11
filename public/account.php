<html>
	<head>
		<?php include('header.php');?>
	</head>
	<body>
	<br>
	<form id="identification" method="post" action="identification.php">
		<div class="accountbox">
			<h1>Account settings</h1>

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
			</script>

			<h1>New email:</h1>
			<input type="email" name="email" value="arthur.chazal@gmail.com" onFocus="field_focus(this, 'arthur.chazal@gmail.com');" onblur="field_blur(this, 'arthur.chazal@gmail.com');" class="formular" />
			<br>
			<h1>New password:</h1>
			<input type="password" name="" value="" class="formular" />
			<br>
			<h1>Old password:</h1>
			<input type="password" name="" value="" class="formular" />
			<input hidden="true" name="formType" id="formType" />
			
			<a onclick="handleClick('update');" ><div class="accountbtn">Change my settings</div></a>
			<a onclick="handleClick('delete');" ><div class="deletebtn">DELETE MY ACCOUNT</div></a>
		  
		</div>
	  
	</form>


	<?php include('footer.php');?>
	</body>
</html>
