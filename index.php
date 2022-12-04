<?php
	session_start();
	
	if(!isset($_SESSION["user_id"])) {
		echo "Hello guest!";
		?>
		
		<br>
		<a href= "/register.php"> register </a>
		<br>
		<a href= "/login.php"> login </a>
		<br>
		<?php
	} else {
		echo "Hello " . $_SESSION["user_name"] . "!";
		if($_SESSION["user_posts"])
		{
			?>
				<br>
				<a href = "/createPost.php"> Create a new recipe </a>
				<br>
			<?php
		}
		?>
		<br>
			<a href = "/logout.php">Logout</a>
		<br>
		<?php
	}
	?>
	<a href = "/recipeList.php"> View recipes </a>
	<?php
?>
