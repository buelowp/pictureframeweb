<?php
	$error_encountered = false;
	libxml_use_internal_errors(true);

	if (isset($_POST['targetdir'])) {
		$target_file = $_POST['targetdir'] . basename($_FILES["image"]["name"]);
		$target_thum = $_POST['targetdir'] . "thumbnails/";
		$contentxml = $_POST['targetdir'] . "contentlist.xml";

		if (uploadFile($target_file, $target_file, $_POST['targetdir'])) {
			if (createThumbs($_POST['targetdir'], basename($_FILES["image"]["name"]), $target_thum, 150) == false) {
				echo "Unable to create a thumbnail for " . basename($_FILES["image"]["name"]);
				$error_ecountered = true;
			}
			if (updateContentXML($contentxml, $target_file, $_POST['targetdir']) == 0) {
				echo "Unable to modify " . $contentxml . "<br>";
				$error_encountered = true;
			}
		}

		$returnurl = parentPath() . "index.php";

		if (!$error_encountered) {
			echo "<form action=\"imagemanager.php\" method=\"post\" name=\"frm\">";
			echo "<input type=\"hidden\" name=\"user\" value=\"" . $_POST['user'] . "\">";
			echo "</form></body></html>";
			echo "<script type=\"text/javascript\">document.frm.submit();</script>";
		}
	}
	else {
		if (!isset($_POST['targetdir'])) {
			echo "Target directory not set";
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

		$pos = strrpos($absolute_url, "/");
		if ($pos === false) {
			echo "Invalid URL passed for location<br>";
		}
		else {
			$urlpath = substr($absolute_url, 0, $pos);
		}
		return $urlpath . "/";
	}

	function display_xml_error($error, $xml)
	{
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
		return $rval;
	}

	function uploadFile($file, $path)
	{
		$imageFileType = pathinfo($file, PATHINFO_EXTENSION);

		$check = getimagesize($_FILES["image"]["tmp_name"]);
		if($check == false) {
			echo "File is not an image.";
			return 0;
		}

		// Check if file already exists
		if (file_exists($file)) {
			echo "Sorry, file already exists.<br>";
			return 0;
		}

		// Check file size
		if ($_FILES["image"]["size"] > 25500000) {
			echo "Sorry, your file is too large.<br>";
			return 0;
		}

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "jpeg") {
			echo "Sorry, only JPG, JPEG files are allowed.<br>";
			return 0;
		}

		if (!move_uploaded_file($_FILES["image"]["tmp_name"], $file)) {
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
