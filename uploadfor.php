<!DOCTYPE html>
<html>
<head>
 <title>
<?php echo "Manage images for " . $_POST['user']; ?>
 </title>
 <link rel="stylesheet" type="text/css" href="localstyle.css">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
$( function() {
    $("form").submit(function(){
          var form_data = new FormData(this);
          $.ajax({
            type: "POST",
            url: "upload.php",
            data: {userfile:JSON.stringify(form_data)},
            dataType: "json",
            timeout: 15000,
            success: function( data ) {
                        console.log( "Success" );
            }
        });
        return false;
    });
});
</script>
<body>

<table class="upload">
 <tr>
  <td class="upload">
	<h1>Select the files to be displayed on <?php printf("%s", $_POST['user']); ?>'s picture frame</h1>
	<h2>Must be a .jpg image for now. Should be sized to 1280x800 if possible.</h2>
	<h2>You can add new files here, or delete selected files.</h2>
    <form enctype="multipart/form-data" method="post" id="uploadform">
        <?php
    		$target_dir = "";
    		if (isset($_POST['user'])) {
    			$target_dir = $_POST['user'] . "/";
    		}
    		echo "<input type=\"hidden\" name=\"targetdir\" value=\"" . $target_dir . "\">\n";
    		echo "<input type=\"hidden\" name=\"user\" value=\"" . $_POST['user'] . "\">\n";
      		?>
          <h2>File to upload</h2>
          <input name="userfile" type="file" class='file'/>
          <input type="submit" name="send" value="Upload File" />
      </form>
      <!--
      	<form action="picuploader.php" method="post" enctype="multipart/form-data">
	<table class="uploadform">
	 <tr class="uploadform">
	  <td class="uploadform">Select an Image File to upload</td>
	 <tr class="uploadform">
	  <td class="uploadform">
  	 	<label class="custom-file-upload">Choose Image
  	 	<input name="fileToUpload" id="fileToUpload" type="file"></label>
	  </td>
	 </tr>
	 <tr>
	  <td class="uploadform"><input class="button" id="uploadbutton" value="Upload Image" type="submit" name="submit"></td>
	 </tr>
	</table>
	</form>
-->
  </td>
  <td class="images">
<!--
  <form action="imagedelete.php" method="post" enctype="multipart/form-data">
  -->
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

	<div class="delete"><input class="button" type="submit" value="Delete Selected" name="submit"></div>
    <!--
  </form>
-->
  </td>
</table>

</body>
</html>
