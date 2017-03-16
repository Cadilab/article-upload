<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>


<?php

	session_start();
	error_reporting(E_ALL);
	require 'db.php';

	if(isset($_GET['article']))
	{
		$art_id = $_GET['article'];
	
		$stmt = $connection->prepare("SELECT * FROM images WHERE auth_id = :id");
		$stmt->bindParam(':id', $art_id, PDO::PARAM_STR);
		$stmt->execute();

		if($stmt->rowCount() > 0)
		{
		
			echo '<div class="row">';

			$check = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach( $check as $row )
			{			

				echo '

					  <div class="col-md-4">
					    <div class="thumbnail">
					      <a href="',$row['photo_location'],'">
					        <img src="',$row['photo_location'],'" alt="Lights" style="width:100%">
					      </a>
					    </div>
					  </div>
					  
				';

			
			}
		}
		else
		{
			$_SESSION['sucess'] = "No photos in this article.";
			header("location: index.php");
			exit();
		}
	}
?>

</body>
</html>