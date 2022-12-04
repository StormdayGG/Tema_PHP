<?php
	session_start();
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	function submit_form($title, $text, $ingredients, $quantities, $tags)
	{
		$servername = "localhost";
		$username = "root";
		$password = "alpiAlbut";
		$dbname = "sweet_corner";
		
		
			
		$conn = new mysqli($servername, $username, $password, $dbname);
		if($conn -> connect_error) {
			die("Connection failed: ".$conn->connect_error);
		}
		$sql1 = "INSERT INTO recipe (cook_id, instructions, title) VALUES ('".$_SESSION['user_id']."', '".$text."', '".$title."')";
		if($conn -> query($sql1) == TRUE) {
			$id = $conn -> insert_id;
			$sql = "";
			for($i = 0; $i < count($ingredients); $i ++) {
				$sql .= "INSERT INTO ingredients (name, quantity, recipe_id) VALUES ('".$ingredients[$i]."', '". $quantities[$i]."', '".$id."')";
				if ($i < count($ingredients) - 1)
					$sql .= ";";
			}
			if($conn -> multi_query($sql) != TRUE) {
				die("Error uploading ingredients");
			}
			while($conn -> next_result());
			$sql = "";
			for($i = 0; $i < count($tags); $i ++) {
				$sql .= "INSERT INTO tags (name, recipe_id) VALUES ('".$tags[$i]."', '".$id."')";
				if ($i < count($tags) - 1)
					$sql .= ";";
			}
			echo $sql;
			if($conn -> multi_query($sql) != TRUE) {
				die("Error uploading tags");
			}
			else
			{
				?>
				<script>
					location.replace("index.php?recipe_creation=success");
				</script>
				<?php
			}
		}
		else
		{
			die("Error uploading recipe");
		}
	}
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$ingredients = array();
		$quantities = array();
		$tags = array();
		foreach ($_POST as $key => $value) {
			
			echo $key;
			?>
			<br/>
			<?php
			echo $value;
			?>
			<br/>
			<br/>
			<?php
			if(strpos("aaa".$key, "ingredient") != FALSE)
			{
				
				$ingredients[] = test_input($value);
			}
			if(strpos("aaa".$key, 'quantity') != FALSE)
			{
				$quantities[] = test_input($value);
			}
			if(strpos("aaa".$key, 'tag') != FALSE)
			{
				$tags[] = test_input($value);
			}
		}
		
		print_r($ingredients);
		print_r($quantities);
		print_r($tags);
		$title = "default";
		$text = "default";
		if(!empty($_POST['titleInput']))
		{
			$title = test_input($_POST['titleInput']);
			
			if(!empty($_POST['recipeInput']))
			{
				$text = test_input($_POST['recipeInput']);
				submit_form($title, $text, $ingredients, $quantities, $tags);
			}
			else
			{
				echo 'Error: Recipe left empty';
			}
		}
		else
		{
			echo 'Error: Title left empty';
		}
		
	}
	
	if(!isset($_SESSION["user_id"])) {
		?>
			<p> You need to be logged in before you can create a new recipe! </p>
			<a href = "login.php"> Log in </a>
		<?php
	}
	else 
	{
		
		?>
		<div id = "ingredientReadRoot" style = "display: none">
			<input type = "button" value = "Remove Ingredient"
				onclick = "this.parentNode.parentNode.removeChild(this.parentNode);" />
			<input name = "ingredient" placeholder = "Ingredient Name" type = "text"/>
			<input name = "quantity" placeholder = "Quantity" type = "text"/>
			<br/>
			<br/>
		</div>
		
		<div id = "tagReadRoot" style = "display: none">
			<input type = "button" value = "Remove Tag"
				onclick = "this.parentNode.parentNode.removeChild(this.parentNode);" />
			<input name = "tag" placeholder = "Tag Name" type = "text"/>
			<br/>
			<br/>
		</div>
		<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<p id = "titleHeader"> Recipe Title </p>
			<input type = "text" id = "titleInput" name = "titleInput" placeholder = "Recipe Title"/>
			<br/>
			<p id = "recipeHeader"> Recipe Steps </p>
			<textarea rows = "8" cols = "40" id = "recipeInput" name = "recipeInput" placeholder = "Write the content of the recipe here"> </textarea>
			<p> To Do: Add Photo Upload </p>
			
			<p id = "ingredientHeader">Ingredients: </p>
			<span id = "ingredientWriteRoot"></span>
			
			<input type = "button" id = "moreIngredientsButton" value = "Add another Ingredient"/>
			
			<p id = "tagHeader">Tags: </p>
			<span id = "tagWriteRoot"></span>
			
			<input type = "button" id = "moreTagsButton" value = "Add another Tag"/>
			<br/>
			<br/>
			<input type = "submit" value = "Submit"/>
		</form>
		
		<script>
			var ingredientCounter = 0;
			function moreIngredients() {
				ingredientCounter ++;
				var newFields = document.getElementById('ingredientReadRoot').cloneNode(true);
				newFields.id = '';
				newFields.style.display = 'block';
				var newField = newFields.childNodes;
				for (var i=0;i<newField.length;i++) {
					var theName = newField[i].name
					if (theName)
						newField[i].name = theName + ingredientCounter;
				}
				var insertHere = document.getElementById('ingredientWriteRoot');
				insertHere.parentNode.insertBefore(newFields,insertHere);
			}
			document.getElementById('moreIngredientsButton').onclick = moreIngredients;
			
			
			var tagCounter = 0;
			function moreTags() {
				tagCounter ++;
				var newFields = document.getElementById('tagReadRoot').cloneNode(true);
				newFields.id = '';
				newFields.style.display = 'block';
				var newField = newFields.childNodes;
				for (var i=0;i<newField.length;i++) {
					var theName = newField[i].name
					if (theName)
						newField[i].name = theName + tagCounter;
				}
				var insertHere = document.getElementById('tagWriteRoot');
				insertHere.parentNode.insertBefore(newFields,insertHere);
			}
			document.getElementById('moreTagsButton').onclick = moreTags;
			
			function initialize() {
				moreIngredients();
				moreTags();
			}
			window.onload = initialize;
		</script>
		<?php
	}
?>
