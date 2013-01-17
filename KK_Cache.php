<?php
/**
 * Cache
 * 
 * This class contains things related to Caching
 *
 * @author Kevin Korb
 */
Class KK_Cache {
	
	/**
	 * No Cache Headers
	 * 
	 * This just outputs headers to the browser to force the browser to reload
	 * the and not cache anything.
	 *
	 */
	public static function no_cache_headers() {
		// Date in the past
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		
		// always modified
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		
		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		
		// HTTP/1.0
		header("Pragma: no-cache");
	}
	
}
?>