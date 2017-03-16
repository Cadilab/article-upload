<?php

	session_start();
	error_reporting(E_ALL);
	require 'db.php';

	function getExtension($str)
	{
	         $i = strrpos($str,".");
	         if (!$i) { return ""; }
	         $l = strlen($str) - $i;
	         $ext = substr($str,$i+1,$l);
	         return $ext;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if(isset($_POST['product_submit']))
		{
			if(!empty($_POST['product_name']) && !empty($_POST['product_author']) && !empty($_POST['product_price']) && empty($_POST['product_search']))
			{
				if(is_numeric($_POST['product_price']))
				{
					$auth_key = round(microtime(true));

					if(isset($_FILES['photos']) && !empty($_FILES['photos']))
					{
						$image_path = "product_images";

						foreach ($_FILES['photos']['name'] as $name => $value)
						{

							$filename = stripslashes($_FILES['photos']['name'][$name]);
							$extension = getExtension($filename);
         					$extension = strtolower($extension);

         					if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
         					{
								$_SESSION['sucess'] = "Invalid extension.";

								header("location: index.php");
								exit();
         					}
         					else
         					{
         						$size = filesize($_FILES['photos']['tmp_name'][$name]);

         						if($size > 5120000)
         						{
									$_SESSION['sucess'] = "You have exceeded the size limit.";

									header("location: index.php");
									exit();
         						}

         						$temp = explode('.', $filename);
        						$newfilename = mt_rand() . '_product.' . end($temp);
        						$name_path = "product_images/".$newfilename;

        						$suc = move_uploaded_file($_FILES['photos']['tmp_name'][$name], $name_path);		


        						if($suc)
        						{
        							$stmt = $connection->prepare("INSERT INTO images (auth_id, photo_location) VALUES (:code, :location)");
									$stmt->bindParam(':code', $auth_key, PDO::PARAM_STR);
									$stmt->bindParam(':location', $name_path, PDO::PARAM_STR);
									$stmt->execute();		
        						}
        						else
        						{
									$_SESSION['sucess'] = "Something went wrong!";

									header("location: index.php");
									exit();             							
        						}
        					}	
         				}	
					}

					$query = "INSERT INTO products (name, author, price, date, code) VALUES (:name, :author, :price, NOW(), :code)";
					$stmt = $connection->prepare($query);

					$stmt->bindParam(':name', $_POST['product_name'], PDO::PARAM_STR);
					$stmt->bindParam(':author', $_POST['product_author'], PDO::PARAM_STR);
					$stmt->bindParam(':price', $_POST['product_price'], PDO::PARAM_STR);
					$stmt->bindParam(':code', $auth_key, PDO::PARAM_STR);

					$stmt->execute();

					if($stmt)
					{
						$_SESSION['sucess'] = "Data inserted to database.";

						header("location: index.php");
						exit();
					}
					else
					{
						$_SESSION['error'] = "Error while submiting data to database.";

						header("location: index.php");
						exit();
					}
				}
			}
			elseif (empty($_POST['product_name']) && empty($_POST['product_author']) && empty($_POST['product_price']) && !empty($_POST['product_search']))
			{
				$_SESSION['error'] = "You can't leave anything empty!";

				header("location: index.php");
				exit();
			}
		}
	}

	// deleting and other things
	if(isset($_GET['action']))
	{
		if($_GET['action']  == "delete_input")
		{
			require 'delete_input.php';
		}
		elseif ($_GET['action']  == "change_input")
		{
			require 'change_input.php';
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

			if(isset($_SESSION['sucess']))
			{
				echo '
					<p>', $_SESSION['sucess'] ,'</p>
				';
			}
			unset($_SESSION['sucess']);
		?>

		<form method="POST" enctype="multipart/form-data">
			<div class='clearfix'>
				<input type="text" name="product_name" placeholder="Product name">
				<input type="text" name="product_author" placeholder="Product author">
				<input type="text" name="product_price" placeholder="Product price"><br /><br />
				<input multiple="multiple" type="file" name="photos[]"/>

				<br /><input type="submit" name="product_submit" value="Add product">
			</div>	
		</form>

		<br /> <br />


			<?php

				$stmt = $connection->prepare("SELECT * FROM products ORDER BY id DESC");
				$stmt->execute();

				if($stmt->rowCount() > 0)
				{

					echo ' <br /><table class="table table-striped">
						    <thead>
						      <tr>
						        <th>Product name</th>
						        <th>Authors Name</th>
						        <th>Price</th>
						        <th>Added on</th>
						        <th>Action</th>
						      </tr>
						    </thead>
						<tbody> ';

					$check = $stmt->fetchAll(PDO::FETCH_ASSOC);

					foreach( $check as $row )
					{

						echo '

						   <tr>
					        <td>',$row['name'],'</td>
					        <td>',$row['author'],'</td>
					        <td>$',$row['price'],'</td>
					        <td>',$row['date'],'</td>
					        <td><a href="index.php?action=delete_input&input_id=',$row['code'],'">Delete</a> / <a href="change_input.php?input_id=',$row['id'],'">Edit</a> / <a href="view_photos.php?article=',$row['code'],'">Photos</a></td>
					      </tr>

						';
					}
					echo '

						</tbody>
						</table>

					';
				}
				else
				{
					echo '
						<br /><p>Table is empty.</p>
					';
				}
			?>	
</div>



</body>
</html>