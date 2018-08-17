<?php
	echo "<html><body>";
	foreach($_POST['checkbox'] as $value) {
		$target_img = $_POST['user'] . "/" . $value;
		$thumb_img = $_POST['user'] . "/thumbnails/" . $value;
		$xmlfile = $_POST['user'] . "/contentlist.xml";
		deleteImageFromXML($xmlfile, $value);
		unlink($thumb_img);
		unlink($target_img);
	}

	echo "<form action=\"imagemanager.php\" method=\"post\" name=\"frm\">";
	echo "<input type=\"hidden\" name=\"user\" value=\"" . $_POST['user'] . "\">";
	echo "</form></body></html>";
	echo "<script type=\"text/javascript\">document.frm.submit();</script>";

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

	function deleteImageFromXML($xml, $target)
	{
		$dom = new DOMDocument('1.0');
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;

		if (!$dom->load($xml)) {
			printf("Unable to load %s<br>", $xml);
			$errors = libxml_get_errors();
			foreach ($errors as $error) {
				$xmlstr = $dom->saveXML();
				$str = explode($xmlstr);
				echo display_xml_error($error, $str);
			}
			return 0;
		}

		$root = $dom->documentElement;
		$domNodeList = $dom->getElementsByTagname('Image');
		$toRemove = array();
		foreach ($domNodeList as $node) {
			if ($node->hasChildNodes()) {
				$u = $node->firstChild;
				if (strstr($u->textContent, $target)) {
					echo "Found URL " . $u->textContent . "</br>";
					$node->removeChild($u);
					$parent = $node->parentNode;
					$parent->removeChild($node);
				}
			}
		}
		$rval = $dom->save($xml);
		if ($rval == 0) {
			printf("Error saving %s<br>", $xml);
			foreach (libxml_get_errors() as $error) {
				echo display_xml_error($error, $dom);
			}
		}

		return 1;
	}
?>
