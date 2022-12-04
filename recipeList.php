<?php
	session_start();
	$servername = "localhost";
	$username = "root";
	$password = "alpiAlbut";
	$dbname = "sweet_corner";
	
	
		
	$conn = new mysqli($servername, $username, $password, $dbname);
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
