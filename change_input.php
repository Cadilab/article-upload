<?php

	session_start();
	require 'db.php';

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if(isset($_POST['product_edit']))
		{
			if(!empty($_POST['product_name']) && !empty($_POST['product_author']) && !empty($_POST['product_price']) && empty($_POST['product_search']))
			{
				if(is_numeric($_POST['product_price']))
				{
					$id = $_GET['input_id'];

					$stmt = $connection->prepare("UPDATE products SET name = :name, author = :author, price = :price WHERE id = :id");
					$stmt->bindParam(":id", $id, PDO::PARAM_STR);
					$stmt->bindParam(':name', $_POST['product_name'], PDO::PARAM_STR);
					$stmt->bindParam(':author', $_POST['product_author'], PDO::PARAM_STR);
					$stmt->bindParam(':price', $_POST['product_price'], PDO::PARAM_STR);

					$stmt->execute();

			 		$_SESSION['sucess'] = "Data edited!";
			 		header("location: index.php");
			 		exit();

				}
			}
		}
	}
?>	

<!DOCTYPE html>
<html>
<head>
	<title>My Input List</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>

<div class="container-fluid">
	<div class="jumbotron">

<?php

if(isset($_GET['input_id']))
{
	$id = $_GET['input_id'];

	$stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_STR);
	$stmt->execute();

	if($stmt->rowCount() > 0)
	{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		echo '

		<form method="POST">
			<div class="clearfix">
				<input type="text" name="product_name" value="', $row['name'] ,'">
				<input type="text" name="product_author" value="', $row['author'] ,'">
				<input type="text" name="product_price" value="', $row['price'] ,'">
				<input type="submit" name="product_edit" value="Edit product">
			</div>	
		</form>

		';
	}
	else
	{
 		$_SESSION['sucess'] = "Error loading content";
 		header("location: index.php");
 		exit();
	}
}
else
{
 		$_SESSION['sucess'] = "Error loading content";
 		header("location: index.php");
 		exit();	
}

?>

</div>
</div>

</body>

</html>