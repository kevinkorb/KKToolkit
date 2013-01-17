<?php
/**
 * File Download
 * 
 * Many times you need to prompt somebody to download
 * the string of data you have as a file and name the file
 * for them.
 * 
 * This isn't exactly rocket science... but it's handy.
 *
 * @author Kevin Korb
 */
Class KK_FileDownload {
	
	/**
	 * Download
	 * 
	 * Simply pass in the string of data you want to be
	 * in the file and then the filename and it will
	 * prompt the user to download it. (Just make sure
	 * this is called before anything is transferred to the
	 * browser.
	 *
	 * @param string $string
	 * @param string $filename
	 */
	public static function download(&$string, $filename) {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: private");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$filename");
		header("Accept-Ranges: bytes");
		
		echo $string;
		exit;
	}
	
}
?>