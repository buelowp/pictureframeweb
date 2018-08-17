<!DOCTYPE html>
<html>
<head>
 <title>
<?php echo "Manage images for " . $_POST['user']; ?>
 </title>
 <link rel="stylesheet" href="imageupload.css" type="text/css">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="script.js" type="text/javascript"></script>
<body>

<table class="imagemanager">
 <tr>
  <td class="chooser">
	<h1>Select the files to be displayed on <?php printf("%s", $_POST['user']); ?>'s picture frame</h1>
	<h2>Selections limited to JPEG images</h2>
    <h2>Images should be 1280x800 or 16:10 aspect ratio</h2>
    <form id="uploadform" action="uploader.php" method="post" enctype="multipart/form-data">
        <?php
            $target_dir = "";
            if (isset($_POST['user'])) {
                $target_dir = $_POST['user'] . "/";
            }
            echo "<input type=\"hidden\" name=\"targetdir\" value=\"" . $target_dir . "\">\n";
            echo "<input type=\"hidden\" name=\"user\" value=\"" . $_POST['user'] . "\">\n";
        ?>

        <input id="uploadImage" type="file" accept="image/*" name="image" onchange="updateFilename()"/>
        <label for="uploadImage" class="custom-file-upload">Select File</label>
        <div id="preview" class="file_input_display"></div>
        <input id="submit" type="submit" name="submit" value="Submit" />
    </form>
    <div id="err"></div>
  </td>
  <td class="images">
  <form action="imagedelete.php" method="post" enctype="multipart/form-data">
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
	echo "<input type=\"hidden\" name=\"user\" value=\"" . $_POST['user'] . "\">";

	function build_thumb_contents($image)
	{
		printf("  <td class=\"imagebox\">\n");
		printf("   <table class=\"thumbnail\">\n");
		printf("    <tr class=\"trthumbtop\">\n");
		printf("     <td class=\"tdthumbtop\"><img src=\"%s\"/></td>\n", $image);
		printf("    </tr>\n    <tr class=\"trthumbbottom\">\n");
		printf("     <td class=\"tdthumbbottom\">\n");
		printf("      <label class=\"checkbox\">\n");
		printf("      <input type=\"checkbox\" name=\"checkbox[]\" value=\"%s\" />", basename($image), basename($image));
		printf("%s\n", basename($image));
		printf("      </label>");
		printf("    </tr>\n");
		printf("   </table>\n");
		printf("  </td>\n");
	}
?>

	<div class="deletebutton"><input id="deletebutton" class="button" type="submit" value="Delete Selected" name="submit"></div>
  </form>
  </td>
</table>

</body>
</html>
