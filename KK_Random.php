<?php
/**
 * Random
 * 
 * This will contain any random number / character
 * generators or anything else that may fit the 'random'
 * category.
 * 
 * Keep in mind... by random, I'm not meaning Miscellenaous...
 * 
 * @author Kevin Korb
 */
Class KK_Random {
	
	/**
	 * Creates a string of random characters.
	 * 
	 * This is just using lowercase letters and numbers 1-9.
	 * 
	 * I didn't want to use 0 as it may conflict with o and whatnot.
	 *
	 * @param int $count
	 * @return string
	 */
	static public function random_characters($count = 9) {
		$string = '';
		$characters = 'abcdefghijklmnopqrstuvwxyz123456789';
		for($c=0; $c<$count; $c++) {
			$string .= $characters[rand(0, 34)];
		}
		return $string;		
	}
	
	/**
	 * This only works for indexed arrays
	 *
	 * @param unknown_type $array
	 */
	public static function ashuffle($array) {
		$total = count($array);
		$randArray = array();
		for($c=0; $c< $total; $c++) {
			$randArray[] = $c;
		}
		shuffle($randArray);
		$returnArray = array();
		foreach($randArray AS $key => $val) {
			$returnArray[$val] = $array[$val];
		}
		return $returnArray;
//		KK_Debug::output_array($randArray);
	}
	
	public static function getShuffleMap($array) {
		$total = count($array);
		$return = array();
		for ($c = 0; $c < $total; $c++) {
			$return[] = $c;
		}
		shuffle($return);
		return $return;
	}
	
	/**
	 * Selects a random images from the directory passed to it
	 * 
	 *
	 * @param string $dir
	 * @return string
	 */
	
	static public function random_image($dir) {
		if($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if(!is_dir($dir.$file)) {
						$fileArr[] = $file;
					}
				}
			}
			closedir($handle);
		} else {
			die("Cannot open $dir.");
		}
		
		$rand = rand(0, (count($fileArr)-1));
		
		$randFile = $fileArr[$rand];
		
		return $randFile;
		
	}
	
}
?>