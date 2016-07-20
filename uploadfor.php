<!DOCTYPE html>
<html>
<head>
 <link rel="stylesheet" type="text/css" href="localstyle.css">
</head>
<body>

<table class="upload">
 <tr>
  <td class="upload">
	<h1>Select the files to be displayed on <?php printf("%s", $_POST['user']); ?>'s picture frame</h1>
	<h2>Must be a .jpg image for now. Should be sized to 1280x800 if possible.</h2>
	<h2>You can add new files here, or delete selected files.</h2>
	<form action="picuploader.php" method="post" enctype="multipart/form-data">
	<table class="uploadform">
	 <tr class="uploadform">
	  <td class="uploadform">Select an Image File to upload</td>
	 <tr class="uploadform">
	  <td class="uploadform">
	 	<label class="custom-file-upload">Choose Image<input type="file" name="fileToUpload" id="fileToUpload"></label>
	  </td>
	 </tr>
	 <tr>
	  <td class="uploadform"><input class="button" type="submit" value="Upload Image" name="submit"></td>
	 </tr>
	</table>
	<?php 
		if (isset($_POST['user'])) {
			$target_dir = $_POST['user'] . "/";
		}
		echo "<input type=\"hidden\" name=\"targetdir\" value=\"" . $target_dir . "\">"
	?>
	</form>
  </td>
  <td class="images">
<?php
	$dirname = $target_dir . "thumbnails/";
	$images = glob($dirname . "*.jpg");
	$count = 0;
	$hastr = true;
	
	printf("<table class=\"imagebox\">\n");
	foreach($images as $image) {
		if (($count++ % 4) == 0) {
			printf(" <tr class=\"imagebox\">\n");
			build_thumb_contents($image);
			$hastr = false;
		}
		else if (($count % 4) == 1) {
			build_thumb_contents($image);
			printf("   </tr>\n", $image, basename($image));
			$hastr = true;
		}
		else {
			build_thumb_contents($image);
			$hastr = false;
		}
	}
	if (!$hastr) {
		printf(" </tr>\n</table>\n");
	}
	
	function build_thumb_contents($image)
	{
		printf("  <td class=\"imagebox\">\n");
		printf("   <table class=\"thumbnail\">\n");
		printf("    <tr class=\"trthumbtop\">\n");
		printf("     <td class=\"tdthumbtop\"><img src=\"%s\"/></td>\n", $image);
		printf("    </tr>\n    <tr class=\"trthumbbottom\">\n");
		printf("     <td class=\"tdthumbbottom\"><div class=\"bottomtext\">%s</div></>\n", basename($image));
		printf("    </tr>\n");
		printf("   </table>\n");
		printf("  </td>\n");
	}
?>
  </td>
</table>

</body>
</html>
