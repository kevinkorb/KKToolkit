<?php
class KK_Filesystem_DirectoryIterator {
	
	public static function get_all_filenames($directory, $recursive = true) {
		$files = array();
		if($recursive === false) {
			$it = new DirectoryIterator($directory);
			foreach($it AS $file) {
				if($file->isFile() === true) {
					$files[] = $file->getPathname();
				}
			}
			sort($files);
			return $files;			
		}
		else {
			$it = new RecursiveDirectoryIterator($directory);
			foreach(new RecursiveIteratorIterator($it) as $file) {
				$files[] = $file->getPathname();
				
			}
			sort($files);
			return $files;			
		}

	}
	
}
?>