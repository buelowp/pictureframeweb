<?php
	$error_encountered = false;
	echo "<html><body>";
	libxml_use_internal_errors(true);

	if (isset($_POST['targetdir']) && isset($_POST['submit'])) {
		$target_file = $_POST['targetdir'] . basename($_FILES["fileToUpload"]["name"]);
		$target_thum = $_POST['targetdir'] . "thumbnails/";
		$contentxml = $_POST['targetdir'] . "contentlist.xml";
		echo "Uploading " . $target_file . "<br>";
		echo "Updating " . $contentxml . "<br>";

		if (uploadFile($target_file, $target_file, $_POST['targetdir'])) {
			if (createThumbs($_POST['targetdir'], basename($_FILES["fileToUpload"]["name"]), $target_thum, 150) == false) {
				echo "Unable to create a thumbnail for " . basename($_FILES["fileToUpload"]["name"]);
				$error_ecountered = true;
			}
			if (updateContentXML($contentxml, $target_file, $_POST['targetdir']) == 0) {
				echo "Unable to modify " . $contentxml . "<br>";
				$error_encountered = true;
			}
		}
		$returnurl = parentPath() . "index.php";

		if (!$error_encountered) {
			echo "<form action=\"uploadfor.php\" method=\"post\" name=\"frm\">";
			echo "<input type=\"hidden\" name=\"user\" value=\"" . $_POST['user'] . "\">";
			echo "</form></body></html>";
			echo "<script type=\"text/javascript\">document.frm.submit();</script>";
		}
	}

	function url_origin( $s, $use_forwarded_host = false )
	{
		$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
		$sp       = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port     = $s['SERVER_PORT'];
		$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
		$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
		$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
		return $protocol . '://' . $host;
	}

	function full_url( $s, $use_forwarded_host = false )
	{
		return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
	}

	function parentPath()
	{
		$absolute_url = full_url( $_SERVER );
		$urlpath = "";

		echo "Local URL is " . $absolute_url . "<br>";
		$pos = strrpos($absolute_url, "/");
		if ($pos === false) {
			echo "Invalid URL passed for location<br>";
		}
		else {
			$urlpath = substr($absolute_url, 0, $pos);
			echo "Local parent URL: " . $urlpath . "<br>";
		}
		return $urlpath . "/";
	}

	function display_xml_error($error, $xml)
	{
		if ($error->line != 0) {
			$return  = $xml[$error->line - 1] . "<br>";
			$return .= str_repeat('-', $error->column) . "^<br>";

			switch ($error->level) {
				case LIBXML_ERR_WARNING:
					$return .= "Warning $error->code: ";
					break;
				case LIBXML_ERR_ERROR:
					$return .= "Error $error->code: ";
					break;
				case LIBXML_ERR_FATAL:
					$return .= "Fatal Error $error->code: ";
					break;
			}

			$return .= trim($error->message) .
			"<br>  Line: $error->line" .
			"<br>  Column: $error->column";

			if ($error->file) {
				$return .= "<br>  File: $error->file";
			}

			return "$return<br>";
		}
		else {
			return "Unknown error<br>";
		}
	}

	function updateContentXML($xml, $file, $path)
	{
		$dom = new DOMDocument('1.0');
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;

		if (!$dom->load($xml)) {
			printf("Unable to load %s<br>", $xml);
			$errors = libxml_get_errors();
			foreach ($errors as $error) {
				$xmlstr = $dom->saveXML();
				$str = explode($xmlstr, " ");
				echo display_xml_error($error, $str);
			}
			return 0;
		}

		$root = $dom->documentElement;
		$u = parentPath() . $file;
		echo "Updating xml to include URL " . $u . "<br>";
		$url = $dom->createElement("Url", $u);
		$image = $dom->createElement("Image");
		$image->appendChild($url);
		$root->appendChild($image);
		$rval = $dom->save($xml);
		if ($rval == 0) {
			printf("Error saving %s<br>", $xml);
			foreach (libxml_get_errors() as $error) {
				echo display_xml_error($error, $dom);
			}
		}
		printf("Saved %d bytes to %s<br>", $rval, $xml);
		return $rval;
	}

	function uploadFile($file, $path)
	{
		$imageFileType = pathinfo($file, PATHINFO_EXTENSION);
		echo "Image file type is " . $imageFileType . "<br>";
		echo "Image destination path is " . $path . "<br>";
		echo "Image name is " . $file . "<br>";
		echo "Temp file name is " . $_FILES["fileToUpload"]["tmp_name"] . "<br>";

		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".<br>";
		} else {
			echo "File is not an image.";
			return 0;
		}

		// Check if file already exists
		if (file_exists($file)) {
			echo "Sorry, file already exists.<br>";
			return 0;
		}

		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 25500000) {
			echo "Sorry, your file is too large.<br>";
			return 0;
		}

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "jpeg") {
			echo "Sorry, only JPG, JPEG files are allowed.<br>";
			return 0;
		}

		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $file)) {
			echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br>";
		}
		else {
			echo "Sorry, there was an error uploading your file.<br>";
			return 0;
		}
		return 1;
	}

	function createThumbs($pathToImages, $fname, $pathToThumbs, $thumbWidth)
	{
		// parse path for the extension
		$info = pathinfo($pathToImages . $fname);
		// continue only if this is a JPEG image
		if ( strtolower($info['extension']) == 'jpg' ) {
			echo "Creating thumbnail for {$fname} <br />";

			// load image and get image size
			$img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
			if ($img) {
				$width = imagesx( $img );
				$height = imagesy( $img );

				// calculate thumbnail size
				$new_width = $thumbWidth;
				$new_height = floor( $height * ( $thumbWidth / $width ) );

				// create a new temporary image
				$tmp_img = imagecreatetruecolor( $new_width, $new_height );

				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

				// save thumbnail into a file
				imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
				imagedestroy($img);
				imagedestroy($tmp_img);
				return true;
			}
		}
		return false;
	}
?>
