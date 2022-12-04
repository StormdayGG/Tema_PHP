
<?php
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	function submit_form($fname, $lname, $email, $passw)
	{
		$servername = "localhost";
		$username = "root";
		$password = "alpiAlbut";
		$dbname = "sweet_corner";
		
		
		$conn = new mysqli($servername, $username, $password, $dbname);
		if($conn -> connect_error) {
			die("Connection failed: ".$conn->connect_error);
		}
		$sql = "INSERT INTO users (first_name, last_name, email, password) VALUES ('".$fname."', '".$lname."', '".$email."', '".$passw."')";
		if($conn -> query($sql) == TRUE) {
			?>
			<script>
				location.replace("login.php?registration=success");
			</script>
			<?php
		}
		else {
			echo "Error: " . $conn->error;
		}
	}
	$fname_error = $lname_error = $email_error = $passw_error = "";
	$fname = $lname = $email = $passw = $repassw = "";
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if(empty($_POST["fname"]))
		{
			$fname_error = "First name is required";
		}
		else
		{
			$fname = test_input($_POST["fname"]);
			if(!preg_match("/^[a-zA-Z-' ]*$/",$fname))
			{
				$fname_error = "Only letters and white space allowed"; 
			}
		}
		if(empty($_POST["lname"]))
		{
			$lname_error = "Last name is required";
		}
		else
		{
			$lname = test_input($_POST["lname"]);
			if(!preg_match("/^[a-zA-Z-' ]*$/",$lname))
			{
				$lname_error = "Only letters and white space allowed"; 
			}
		}
		if(empty($_POST["email"]))
		{
			$email_error = "Email is required";
		}
		else
		{
			$email = test_input($_POST["email"]);
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$email_error = "Invalid email format";
			}
		}
		if(empty($_POST["passw"]))
		{
			$passw_error = "Password is required";
		}
		else
			$passw = test_input($_POST["passw"]);
		if(!empty($_POST["repassw"]))
			$repassw = test_input($_POST["repassw"]);
		if($passw != $repassw)
		{
			$passw_error = "Passwords don't match";
		}
		$ok = 0;
		if($fname_error == "" && $lname_error == "" && $email_error == "" && $passw_error == "")
			$ok = 1;
		if($ok == 1)
			submit_form($fname, $lname, $email, $passw);
	}
?>

<html>
	<head>
		<style>
			.error_message {
				color: red;
				font-size: 16px;
			}
		</style>
	</head>
	<body>
		<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "post">
			<label for = "fname"> First Name: </label> <br>
			<input type = "text" id = "fname" name = "fname" value = "<?php echo $fname;?>"> <br>
			<?php
			if($fname_error != "")
			{
				?>
				<span class="error_message"> <?php echo $fname_error; ?> </span> <br>
				<?php
			}
			?>
			<label for = "lname"> Last Name: </label> <br>
			<input type = "text" id = "lname" name = "lname" value = "<?php echo $lname;?>"> <br>
			<?php
			if($lname_error != "")
			{
				?>
				<span class="error_message"> <?php echo $lname_error; ?> </span> <br>
				<?php
			}
			?>
			<label for = "email"> Email </label> <br>
			<input type = "text" id = "email" name = "email" value = "<?php echo $email;?>"> <br>
			<?php
			if($email_error != "")
			{
				?>
				<span class="error_message"> <?php echo $email_error; ?> </span> <br>
				<?php
			}
			?>
			<label for = "passw"> Password </label> <br>
			<input type = "password" id = "passw" name = "passw"> <br>
			<?php
			if($passw_error != "")
			{
				?>
				<span class="error_message"> <?php echo $passw_error; ?> </span> <br>
				<?php
			}
			?>
			<label for = "repassw"> Confirm Password </label> <br>
			<input type = "password" id = "repassw" name = "repassw"> <br>
			<br>
			<input type = "submit" id = "submit" name = "submit">
		</form>
	</body>
</html>