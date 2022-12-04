<?php
	session_start();
	//Get Heroku ClearDB connection information
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
	
	$sql = "SELECT r.id, r.title, c.first_name, c.last_name FROM recipe r JOIN users c ON (r.cook_id = c.id)";
	$results = mysqli_query($conn, $sql);
	if($results)
	{
		$titles = array();
		$ids = array();
		$authors = array();
		
		while($row = mysqli_fetch_assoc($results))
		{
			$titles[] = $row['title'];
			$ids[] = $row['id'];
			$authors[] = $row['first_name']." ".$row['last_name'];
		}
	}
	else
		die("Error getting information from database");
?>
<table>
		<tr>
			<th> Recipe Title </th>
			<th> Author </th>
		</tr>
		<span id = "count" style = "display:none"> <?php echo count($titles); ?> </span>
	<?php 
		for($i = 0; $i < count($titles); $i ++)
		{
			?>
				<tr>
					<td id = "<?php echo 'title'.$i;?>" > <a href = "<?php echo 'viewRecipe.php?id='.$ids[$i];?>"> <?php echo $titles[$i]; ?> </a> </td>
					<td id = "<?php echo 'author'.$i;?>" > <?php echo $authors[$i]; ?> </td>
					<span id = "<?php echo 'id'.$i;?>" style = "display:none"> <?php echo $ids[$i]; ?> </span>
				</tr>
			<?php
		}
	?>
</table>
