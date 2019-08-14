<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "");

	$email = isset($_POST["loginName"]) ? $_POST["loginName"] : false;
	$pass = isset($_POST["loginPassw"]) ? $_POST["loginPassw"] : false;

	$target_dir = "uploads/";
	if (isset($_POST["loginName"]))
	{
		$querySelect = "SELECT user_id FROM dbUser.tbUsers WHERE email = '$email' AND password='$pass'";
		$resSelect = mysqli_query($mysqli, $querySelect);
		$resSelect = mysqli_fetch_assoc($resSelect)['user_id'];
	}

	if (isset($_POST["submit"])){
			$target_file = $target_dir . basename($_FILES["picToUpload"]["name"][0]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$check = getimagesize($_FILES["picToUpload"]["tmp_name"][0]);
			if ($check !== false){
				$uploadOk = 1;
			}
			else {
				echo "File is not an image";
				echo "<br/>";
				$uploadOk = 0;
			}

			if (file_exists($target_file))
			{
				echo "Sorry, file already exists";
				echo "<br/>";

				$uploadOk = 0;
			}

			if ($_FILES["picToUpload"]["size"][0] > 1000000)
			{
				echo "Sorry, your file is too large";
				echo "<br/>";

				$uploadOk = 0;
			}

			if ($imageFileType != "jpg" && $imageFileType != "jpeg")
			{
				echo "Sorry, only jpg or jpeg files allowed";
				echo "<br/>";

				$uploadOk = 0;
			}

			if ($uploadOk == 0)
			{
				echo "Sorry, your file was not uploaded";
				echo "<br/>";

			}
			else {
				if (move_uploaded_file($_FILES["picToUpload"]["tmp_name"][0], $target_file)){
					echo "The file " . basename($_FILES["picToUpload"]["name"][0]) . " has been uploaded.";
					echo "<br/>";

					$filename = basename($_FILES['picToUpload']['name'][0]);
					$queryInsert = "INSERT INTO dbUser.tbgallery (user_id, filename) VALUES ('$resSelect', '$filename' )";
					mysqli_query($mysqli, $queryInsert);

				}
				else {
					echo "Sorry, there was an error uploading your file.";
					echo "<br/>";

				}
			}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 3</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Charlotte Elizabeth Jacobs">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM dbUser.tbUsers WHERE email = '$email' AND password='$pass'";
				$res = mysqli_query($mysqli, $query);
				if($res && mysqli_num_rows($res) > 0){
					$row = mysqli_fetch_assoc($res);
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";

					echo 	"<form enctype='multipart/form-data' action='login.php' method='post'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit'/>
									<input type='hidden' name='loginName' value='$email'/>
									<input type='hidden' name='loginPassw' value='$pass'/>
								</div>
						  	</form>
								<h2>Image Gallery</h2>
								<div class = 'row imageGallery'>";

								$queryGal = "SELECT * FROM dbUser.tbgallery WHERE user_id = '$resSelect'";
								$resGal = mysqli_query($mysqli, $queryGal);
								if($resGal && mysqli_num_rows($resGal) > 0){
									while (mysqli_fetch_assoc($resGal) != null)
									{
											$rowGal = mysqli_fetch_assoc($resGal);
											$filename = $rowGal['filename'];
											$filepath = "uploads/$filename";
											echo" <div class='col-3' style='background-image: url($filepath)'> </div>";
									}
								}

						echo"
								</div>
								";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>
