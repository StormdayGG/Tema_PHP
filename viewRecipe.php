<?php
	session_start();
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	if(!empty($_GET["id"]))
	{
		$recipe_id = $_GET["id"];
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
		$sqlR = "SELECT r.title, r.instructions, c.first_name, c.last_name, c.id as user_id FROM recipe r JOIN users c ON (r.cook_id = c.id) WHERE r.id = '".$recipe_id."'";
		$sqlI = "SELECT name, quantity FROM ingredients WHERE recipe_id = '".$recipe_id."'";
		$sqlT = "SELECT name FROM tags WHERE recipe_id = '".$recipe_id."'";
		
		$resultsR = $conn -> query($sqlR);
		$resultsI = $conn -> query($sqlI);
		$resultsT = $conn -> query($sqlT);
		if($resultsR)
		{
			$valuesR = "";
			$valuesI = array();
			$valuesT = array();
			while($row = mysqli_fetch_assoc($resultsR))
			{
				$valuesR = $row;
			}
			while($row = mysqli_fetch_assoc($resultsI))
			{
				$valuesI[] = $row;
			}
			while($row = mysqli_fetch_assoc($resultsT))
			{
				$valuesT[] = $row['name'];
			}
			if($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$action = $_POST['action'];
				$text = "";
				if(isset($_POST['review']))
					$text = $_POST['review'];
				$rating = "";
				if(isset($_POST['rating']))
					$rating = $_POST['rating'];
				$sql = '';
				if(!isset($_SESSION['user_id']))
				{
					?>
						<p> <span style = "color:red"> Error! </span> Can only modify reviews while logged in </p> 
					<?php
				}
				else
				{
					if($action == 'create')
					{
						$sql = 'INSERT INTO review (post_id, rating, text, user_id) VALUES ("'.$recipe_id.'", "'.$rating.'", "'.$text.'", "'.$_SESSION['user_id'].'")';
						if($conn -> query($sql))
						{
							;
						}
						else
							die("Error creating review");
					}
					else
					{
						if(str_contains($action, 'edit'))
						{
							$id = substr($action, 4);
							$sql = 'UPDATE review SET rating = "'.$rating.'", text = "'.$text.'" WHERE id = "'.$id.'" AND user_id = "'.$_SESSION['user_id'].'"';
							if($conn -> query($sql))
							{
								;
							}
							else
								die("Error editing review");
							
						}
						else
						{
							if(str_contains($action, 'delete'))
							{
								$id = substr($action, 6);
								$sql = 'DELETE FROM review WHERE id = "'.$id.'" AND user_id = "'.$_SESSION['user_id'].'"';
								if($conn -> query($sql))
								{
									;
								}
								else
									die("Error deleting review");
								
							}
						}
					}
				}
			}
			$sqlRev = "SELECT r.id, r.rating, r.text, r.user_id, u.first_name, u.last_name FROM review r JOIN users u ON (user_id = u.id) WHERE post_id = ".$recipe_id;
			$resultsRev = $conn -> query($sqlRev);
			$valuesRev = array();
			while($row = mysqli_fetch_assoc($resultsRev))
			{
				$valuesRev[] = $row;
			}
			?>
			<h1> <?php echo $valuesR['title']; ?> </h1>
			<h4> <?php echo "By ".$valuesR['first_name']." ".$valuesR['last_name'];?> </h4>
			<?php
			for($i = 0; $i < count($valuesT); $i ++)
			{
				?>
				<a href="recipeList.php?tag=<?php echo $valuesT[$i]; ?>" style = "padding-right: 5px"><?php echo $valuesT[$i]; ?></a>
				<?php
			}
			?>
			<br/>
			<br/>
			<table>
				<tr>
					<th> Ingredient </th>
					<th> Quantity </th>
				</tr>
				
				<?php
				for($i = 0; $i < count($valuesI); $i ++)
				{
					?>
					<tr>
						<td> <?php echo $valuesI[$i]['name']; ?> </td>
						<td> <?php echo $valuesI[$i]['quantity']; ?> </td>
					</tr>
					<?php
				}
				?>
			</table>
			<p> 
				<?php echo $valuesR['instructions']; ?>
			</p>
			<br>
			<hr/>
			<span id = "nr_reviews" style = "display:none"><?php echo count($valuesRev);?></span>
			<div id = "reviews">
				<?php
					for($i = 0; $i < count($valuesRev); $i ++)
					{
						?>
						<div id = "<?php echo "review_nr".$i; ?>">
							<span id = "<?php echo "review_id".$i; ?>" style = "display:none"> <?php echo $valuesRev[$i]['id']; ?> </span>
							<span> <?php echo $valuesRev[$i]['first_name'].' '.$valuesRev[$i]['last_name'].': ';?></span>
							<span id = "<?php echo "review_rating".$i; ?>"> <?php echo $valuesRev[$i]['rating'];?> </span>
							<span>/ 5</span>
							<?php
							if(isset($_SESSION['user_id'])) if($valuesRev[$i]['user_id'] == $_SESSION['user_id'])
							{
								?>
								<button id = "<?php echo 'editRevButton'.$i; ?>" style = "padding-left: 10px"> Edit Review </button>
								<button id = "<?php echo 'deleteRevButton'.$i; ?>" style = "padding-left: 10px"> Delete Review </button> 
								<?php
							}
							?>
							<p id = "<?php echo "review_text".$i;?>" ><?php echo $valuesRev[$i]['text']; ?></p>
						</div> 
						<span id = "<?php echo "delete_form_anchor".$i;?>" style = "display:none"></span>
						<hr/>
						
						<?php
					}
				?>
			</div>
			<br>
			<?php
			if(isset($_SESSION['user_id']))
			{
				?>
				<p> Tried it? Leave a review! </p>
				<button id = "createReviewButton"> Write a review </button>
				<?php
			}
			?>
			<div id = "review_form" style = "display:none">
				<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$recipe_id;?>">
					<input type = "text" name = "action" value = "nothing"  style = "display:none"/>
					<textarea rows = "8" cols = "40" name = "review" placeholder = "Tried it? How did you find it?"></textarea>
					<br>
					<span> On a scale of 0 to 5, you'd rate it a... </span>
					<span name = "score">2.5</span>
					<br>					
					<input type = "range" name = "rating" min = "0" max = "5" step = "0.1" oninput = "this.previousElementSibling.previousElementSibling.innerHTML = this.value" value = "2.5" />
					<br>
					<input type = "submit" name = "submit"/>
				</form>
			</div>
			<div id = "delete_rev_form" style = "display:none; background-color:#EE8F82">
				<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$recipe_id;?>">
					<input type = "text" name = "action" value = "nothing"  style = "display:none"/>
					<p> Are you sure you want to delete this review? It cannot be undone. </p>
					<input type = "submit" name = "yes" value = "Yes"/>
					<button name = "no"> No </button>
				</form>
			</div>
			<script>
				var formCounter = 0;
				function createReview()
				{
					formCounter ++;
					var newFields = document.getElementById('review_form').cloneNode(true);
					newFields.id = 'form' + formCounter;
					newFields.style.display = 'block';
					var newField = newFields.childNodes;
					newField[1].name = 'reviewForm' + formCounter;
					var newerField = newField[1].childNodes;
					
					for (var i=0;i<newerField.length;i++) {
						var theName = newerField[i].name;
						if(theName)
							newerField[i].id = theName + formCounter;
					}
					var insertHere = document.getElementById('createReviewButton');
					insertHere.parentNode.insertBefore(newFields,insertHere);
					insertHere.style.display = 'none';
					document.getElementById('action' + formCounter).value = 'create';
				}
				function editReview(index)
				{
					formCounter ++;
					var newFields = document.getElementById('review_form').cloneNode(true);
					newFields.id = 'form' + formCounter;
					newFields.style.display = 'block';
					var newField = newFields.childNodes;
					newField[1].name = 'reviewForm' + formCounter;
					var newerField = newField[1].childNodes;
					
					for (var i=0;i<newerField.length;i++) {
						var theName = newerField[i].name;
						if(theName)
							newerField[i].id = theName + formCounter;
					}
					var insertHere = document.getElementById('review_nr' + index);
					insertHere.parentNode.insertBefore(newFields,insertHere);
					insertHere.style.display = 'none';
					document.getElementById('action' + formCounter).value = 'edit' + parseInt(document.getElementById('review_id' + index).innerHTML);
					document.getElementById('review' + formCounter).innerHTML = document.getElementById('review_text' + index).innerHTML;
					document.getElementById('rating' + formCounter).value = parseFloat(document.getElementById('review_rating' + index).innerHTML);
					document.getElementById('rating' + formCounter).previousElementSibling.previousElementSibling.innerHTML = document.getElementById('review_rating' + index).innerHTML;
				}
				function deleteReview(index)
				{
					formCounter ++;
					var newFields = document.getElementById('delete_rev_form').cloneNode(true);
					newFields.id = 'form' + formCounter;
					newFields.style.display = 'block';
					var newField = newFields.childNodes;
					newField[1].name = 'deleteForm' + formCounter;
					var newerField = newField[1].childNodes;
					
					for (var i=0;i<newerField.length;i++) {
						var theName = newerField[i].name;
						if(theName)
							newerField[i].id = theName + formCounter;
					}
					var insertHere = document.getElementById('delete_form_anchor' + index);
					insertHere.parentNode.insertBefore(newFields,insertHere);
					document.getElementById('action' + formCounter).value = 'delete' + parseInt(document.getElementById('review_id' + index).innerHTML);
					document.getElementById('no' + formCounter).onclick = (function(i) { return function() {document.getElementById('no' + i).parentElement.remove();} })(formCounter);
					
				}
				document.getElementById('createReviewButton').onclick = createReview;
				
				
				var revCounter = parseInt(document.getElementById('nr_reviews').innerHTML);
				console.log(revCounter);
				for(var i = 0; i < revCounter; ++ i)
				{
					if(document.getElementById('editRevButton' + i))
					{
						document.getElementById('editRevButton' + i).onclick = (function(i) { return function() {editReview(i);} })(i);
						document.getElementById('deleteRevButton' + i).onclick = (function(i) { return function() {deleteReview(i);} })(i);
					}
				}
			</script>
			<?php
			
		}
		else
		{
			die("Couldn't find the recipe");
		}
	}
	else
	{
		die("No recipe ID provided");
	}
?>
