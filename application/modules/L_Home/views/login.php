<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<form action="<?php echo base_url('Home/sql_injection'); ?>" method="post">
		Email : <input type="text" name="email"><br>
		Password : <input type="password" name="password"><br>
		<input type="submit" name="" value="submit">
	</form>
</body>
</html>