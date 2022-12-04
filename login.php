<?php
	session_start();
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	function submit_form($email, $passw, &$login_error)
	{
		$cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
		$cleardb_server = $cleardb_url["host"];
		$cleardb_username = $cleardb_url["user"];
		$cleardb_password = $cleardb_url["pass"];
		$cleardb_db = substr($cleardb_url["path"],1);
		$active_group = 'default';
		$query_builder = TRUE;
		// Connect to DB
		$conn = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);
		if($conn -> connect_error) {
			die("Connection failed: ".$conn->connect_error);
		}
		$sql = "SELECT id, first_name, last_name, can_moderate, can_post FROM users WHERE email = '".$email."' AND password = '".$passw."'";
		$results = mysqli_query($conn, $sql);
		if($results) {
			$ok = 0;
			$name = "";
			$mods = 0;
			$posts = 0;
			while($row = mysqli_fetch_assoc($results))
			{
				$ok = $row["id"];
				$name = $row["first_name"] . " " . $row["last_name"];
				$mods = $row["can_moderate"];
				$posts = $row["can_post"];
				printf ("%s (%s)\n", $row["id"], $name);
			}
			if($ok)
			{
				$_SESSION["user_id"] = $ok;
				$_SESSION["user_name"] = $name;
				$_SESSION["user_posts"] = $posts;
				$_SESSION["user_mods"] = $mods;
				?>
				<script>
					location.replace("index.php");
				</script>
				<?php
			}
			else
			{
				$login_error = "Invalid email/password!";
			}
		}
		else {
			$login_error = "Invalid email/password!";
			echo "Error: " . $conn->error;
		}
	}
	
	$login_error = "";
	$email = $passw = "";
	
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if(empty($_POST["email"]))
		{
			$login_error = "Email is required";
		}
		else
		{
			$email = test_input($_POST["email"]);
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$login_error = "Invalid email format";
			}
		}
		if(empty($_POST["passw"]))
		{
			$login_error = "Password is required";
		}
		else
			$passw = test_input($_POST["passw"]);
		if($login_error == "")
		{
			submit_form($email, $passw, $login_error);
		}
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
		<?php
			if(!empty($_GET["registration"]))
			{
				$registration_status = $_GET["registration"];
				if($registration_status == "success")
				{
					?>
						<span style = "color: green;"> Successfully registered! </span>
					<?php
				}
			}
		?>
		
		<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "post">
			<?php
			if($login_error != "")
			{
				?>
				<span class="error_message"> <?php echo $login_error; ?> </span> <br>
				<?php
			}
			?>
			
			<label for = "email"> Email </label> <br>
			<input type = "email" id = "email" name = "email" value = "<?php echo $email;?>"> <br>
			
			<label for = "passw"> Password </label> <br>
			<input type = "password" id = "passw" name = "passw"> <br>
			
			<input type = "submit" id = "submit" name = "submit" value = "Login">
		</form>
		<p> No account? Register here </p>
		<a href = "register.php"> Register </a>
	</body>
</html>