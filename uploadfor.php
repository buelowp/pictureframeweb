<!DOCTYPE html>
<html>
<body>

<h1>Select a file to upload for <?php printf("%s<br>", $_POST['user']); ?></h1>
<h2>Must be a .jpg image for now. Should be sized to 1280x800 if possible.</h2>
<form action="picuploader.php" method="post" enctype="multipart/form-data">
Select image to upload:
<input type="file" name="fileToUpload" id="fileToUpload">
<?php 
	if (isset($_POST['user'])) {
		$target_dir = $_POST['user'] . "/";
	}
	echo "<input type=\"hidden\" name=\"targetdir\" value=\"" . $target_dir . "\">"
?>
<input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
