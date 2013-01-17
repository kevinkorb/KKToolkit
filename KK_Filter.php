<?php
/**
 * Filter
 * 
 * This deals with filtering input and some other
 * basic wrapper methods for data filtering.
 * 
 * The majority of this class should be nicely reusable.
 * 
 * @author Kevin Korb
 */
Class KK_Filter {
	
	/**
	 * Get HTML
	 * 
	 * Wrapper function for strip_tags that 
	 * specifies my allowed tags.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function html($string) {
		return strip_tags($string, '<p>,<br>,<b>,<i>,<strong>,<div>,<strike>,<em>,<ul>,<ol>,<li>,<a>');
	}
	
	/**
	 * No HTML
	 * 
	 * This strips all HTML tags.  This is useful for creating
	 * a search index field as well as for places where we're using
	 * user-inputted material but we need to remove all formatting so
	 * that we retain uniformity
	 * 
	 * Clearly just a wrapper for strip_tags, however I didn't want to call
	 * strip_tags directly as I wanted to leave this open for change or customization. 
	 *
	 * @param string $string
	 * @return string
	 */
	public static function no_html($string) {
		return strip_tags($string);
	}
	
	/**
	 * Clean Array
	 * 
	 * This cleans every value in the array against SQL injection.
	 * 
	 * *Revised and added the str_replace.  This was affecting line breaks
	 * going into sql as literal text instead of line breaks.
	 * 
	 * *Revised and made it so that it will now go 2 levels deep in a multidimentional
	 * array.  For instance if there is an array in your html form you'll have $_POST['array']['key'] = "val";
	 *
	 *
	 * @param array $array
	 * @return array
	 */
	public static function clean_array($array) {
		global $db;
		$new_array = array();
		foreach($array AS $key => $val) {
			if(is_array($val)) {
				$new_array[$key] = self::clean_another_array($val);
			}
			else {
				$new_array[$key] = mysqli_real_escape_string($db, $val); 
				$new_array[$key] = str_replace('\r\n','
', $new_array[$key]);
			}
		}
		return $new_array;
	}
	
	/**
	 * Clean Another Array.
	 *
	 * @param array $array
	 * @return array
	 */
	private static function clean_another_array($array) {
		global $db;
		$new_array = array();
		foreach($array AS $key => $val) {

			$new_array[$key] = mysqli_real_escape_string($db, $val);
			$new_array[$key] = str_replace('\r\n','
', $new_array[$key]);
			
		}
		return $new_array;
	}
	
	/**
	 * Remove Breaks
	 * 
	 * Text for javascript can not be broken.
	 *
	 * @param string $str
	 * @return string
	 */
	public static function remove_breaks($str) {
		return preg_replace('`[\r\n]+`',"",$str);
	}
	
	/**
	 * String
	 * 
	 * Basically prevents SQL injection for a string.
	 *
	 * @param string $str
	 * @return string
	 */
	public static function string($str) {
		global $db;
		return mysqli_real_escape_string($db,$str);
	}
	
	/**
	 * Length
	 * 
	 * This is a wrapper function for substr, however
	 * if we do cut-off any of the string, we'll throw three
	 * dots after it.
	 *
	 * @param string $str
	 * @param int $characters
	 * @return string
	 */
	public static function length($str, $characters) {
		if(strlen($str) <= $characters) {
			return $str;
		}
		else {
			return substr($str, 0, $characters)."...";
		}
	}
	
	/**
	 * Date Dots
	 * 
	 * This returns the date in the format of
	 * mm.dd.yy
	 *
	 * @param int $time
	 * @return string
	 */
	public static function date_dots($time) {
		return date("n\.j\.y", $time);
	}
	
	/**
	 * Remove Breaks
	 * 
	 * This is used for text that is shown using javascript.
	 * 
	 * All breaks need to be removed and htmlentities ran on it.
	 *
	 * @param string $str
	 * @return string
	 */
	public static function text_for_js($str) {
		$str = htmlentities($str);
		$str = self::remove_breaks($str);
		$str = addslashes($str);
		return $str;
	}
	
	/**
	 * Valid Email
	 * 
	 * Returns true or false depending if the email is valid or not.
	 *
	 * @param string $email
	 * @return boolean
	 */
	public static function valid_email($email) {

 		 // First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}  
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Obfuscate Email
	 * 
	 * This works to translate an email address into a web-friendly bot-unfriendly
	 * address.
	 * 
	 * Currently it returns every 3rd letter normally, but the others in ASCII code
	 *
	 * @param string $email
	 * @return string
	 */
	public static function obfuscate_email($email) {
		$encrypted_email = '';		
		$len = strlen($email);
		for ($i=0;$i<$len;$i++) {
			if($i%3 == 0) {
				$encrypted_email .= $email[$i];
			}
			else {
				$encrypted_email .= "&#" . ord($email[$i]).";";
			}
			
		}
		return $encrypted_email;
	}
	
	/**
	 * Obfuscate Email Link
	 * 
	 * This works to translate an email address into a web-friendly bot-unfriendly
	 * address.
	 *
	 * @param string $email
	 * @return string
	 */
	public static function obfuscate_email_link($email) {
		$encrypted_email = self::obfuscate_email($email);		
		return "<a href=\"mailto:$encrypted_email\" target=_blank>$encrypted_email</a>";
	}
	
	/**
	 * Array For DB
	 * 
	 * To properly store an array into the db we serialize it
	 * and then encode it in base 64.
	 *
	 * @param array $array
	 * @return string
	 */
	public static function array_for_db($array) {
		$serialized = serialize($array);
		return base64_encode($serialized);
	}
	
	/**
	 * Array From DB
	 * 
	 * This takes our value from the db and returns it
	 * unserialized and unencoded as an array.
	 *
	 * @param string $db_val
	 * @return array
	 */
	public static function array_from_db($db_val) {
		$un_64 = base64_decode($db_val);
		return unserialize($un_64);
	}
	
	/**
	 * Fieldname to Text
	 * 
	 * This returns what should normally be 
	 * a good textual representation of a fieldname.
	 *
	 * @param string $fieldname
	 * @return string
	 */
	public static function field_to_text($fieldname) {
		$string = str_replace('_', ' ', $fieldname);
		return ucwords($string);
	}
	
	public static function nl2br($string) {
		$text = str_replace('\n', '<br>', $string);
		return  str_replace('\r', '', $text);
	}
	
	/**
	 * Check Array For Email.
	 * 
	 * This is used to check everything submitted via a typical
	 * web mail form.  Just throw everything coming from this user into an array
	 * and run the static function passing the array.  If it returns true it is fine.
	 * If it returns false there is a really good chance it is spam.
	 *
	 * @param array $array
	 * @return boolean
	 */
	public static function check_array_for_email($array) {
		foreach($array AS $key => $val) {
			$original = $val;
			$altered = str_ireplace(array( "%3Ca%20href", "%0a", "%0d", "Content-Type:", "bcc:","to:","cc:", "[url", "<a href"), "", $val );
			if($original != $altered) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * PDF Multiline
	 * 
	 * Basically I couldn't get my text to work right.
	 * 
	 * It kept wanting to actually output the \r\n with and without
	 * stripslashes.  This works.  Maybe not the best way?
	 *
	 * @param string $string
	 * @return string
	 */
	public static function pdf_multiline($string) {
		return str_replace('\r\n', '
', $string);
	}
	
	/**
	 * Output If Set
	 *
	 * Simply checks to see if the variable you pass it
	 * is set, if it is it returns you the value, if not,
	 * it returns an empty string
	 * 
	 * @param string $var
	 * @return string
	 */
	public static function output_if_set(&$var, $default_return = '') {
	    if(isset($var)) {
	       return $var;
	    }
	    else {
	       return $default_return;
    	}
	}
	
	/**
	 * Strip Slashes Array -Recursive
	 * 
	 * This will recursivly go through your array
	 * and strip all of the slashes from your values and
	 * then encode the values using html_entities.
	 * You can pass false on the second paramenter to bypass
	 * the html_entities call.
	 *
	 * @param array $array
	 * @param boolean $html_entities
	 * @return array
	 */
	public static function strip_slashes_array($array, $html_entities = true) {
		if(!is_array($array)) {
			$c = print_r(debug_backtrace(), true);
			trigger_error("Non-Array Passed to Strip Slashes Array: ".$c, E_USER_WARNING);
			return $array;
		}
		foreach($array AS $key => $val) {
			if(is_array($val)) {
				$array[$key] = self::strip_slashes_array($val, $html_entities);
			}
			else {
				$array[$key] = stripslashes($val);
				if($html_entities === true) {
					$array[$key] = htmlentities($array[$key]);
				}
			}
		}
		return $array;
	}
	
	/**
	 * Browser Friendly String
	 * 
	 * This just replaces the strange characters that get cut and pasted into web froms like the
	 * slanted quotes or the apostrophie that doen't work as one etc.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function browser_friendly_string($string) {
    	return strtr($string,"\x82\x83\x84\x85\x86\x87\x89\x8a\x8b\x8c\x8e\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9e\x9f","'f\".**^\xa6<\xbc\xb4''\"\"---~ \xa8>\xbd\xb8\xbe");
	}
	
	public static function filterToTenDigitPhone($phone){
		$num = preg_replace("/[^0-9]/", '', $phone);
		if(strlen($num) == 11 && substr($num, 0, 1) == '1')
		{
			$num = substr($num, 1, 10);
		}
		if(strlen($num) != 10)
		{
			return false;
		}
		return $num;
	}
	
	public static function file_friendly($string, $count = 45){
		$string = preg_replace('/[^0-9a-zA-Z-_]/', '-', $string);
		if(strlen($string) > $count) {
			$string = substr($string, 0, $count);
		}
		return $string;
	}
	
}

?>