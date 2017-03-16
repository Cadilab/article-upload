<?php

	if(isset($_GET['input_id']))
	{
		$input_id = $_GET['input_id'];

		$stmt = $connection->prepare("SELECT id FROM products WHERE code = :id");
		$stmt->bindParam(':id', $input_id, PDO::PARAM_STR);
		$stmt->execute();

		if($stmt->rowCount() > 0)
		{
			$query = $connection->prepare("DELETE FROM products WHERE code = :id");
			$query->bindParam(':id', $input_id, PDO::PARAM_STR);
			$query->execute();

			$query = $connection->prepare("DELETE FROM images WHERE auth_id = :id");
			$query->bindParam(':id', $input_id, PDO::PARAM_STR);
			$query->execute();

			$_SESSION['sucess'] = "Input deleted.";

			header("location: index.php");
			exit();
		}
		else
		{
			$_SESSION['sucess'] = "Invalid input id.";

			header("location: index.php");
			exit();			
		}	
	}
	else
	{
		$_SESSION['sucess'] = "Error.";

		header("location: index.php");
		exit();			
	}
?>