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

	public static function get_all_subdirectories($directory) {
		$directories = array();
		$it = new DirectoryIterator($directory);
		foreach($it AS $file) {
			if($file->isDir() === true && !$file->isDot()) {
				if(substr($file->getBasename(), 0, 1) == '.')
				{
					continue;
				}
				$directories[] = $file->getPathname();
			}
		}
		sort($directories);
		return $directories;


	}
	
}
?>