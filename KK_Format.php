<?php
/**
 * CA Format
 * 
 * This will handle our simple formatting of data.
 *
 * @author Kevin Korb
 */
class KK_Format {
	
	/**
	 * Money Format
	 * 
	 * This simply takes a number and returns
	 * it in a format of $ 34,234.53 format with
	 * the dollar sign
	 *
	 * @param float $float
	 * @return string
	 */
	public static function money($float, $decimal_places = 2) {
		return "$ ".number_format($float, $decimal_places, ".", ",");
	}

    public static function time_ago($time)
    {
        if(!is_integer($time))
        {
            $time = strtotime($time);
        }
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        $now = time();

        $difference     = $now - $time;
        $tense         = "ago";

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $periods[$j].= "s";
        }

        return "$difference $periods[$j] ago ";
}

	/**
	 * Percentage
	 * 
	 * Express the value in as a percentage
	 *
	 * @param string $float
	 * @param int $decimal_places
	 * @return string
	 */
	public static function percentage($float, $decimal_places = 0) {
		return round($float*100, $decimal_places)." %";
	}
	
	public static function phone($phone){
		$phone = KK_Filter::filterToTenDigitPhone($phone);
		$areaCode = substr($phone, 0, 3);
		$secondGroup = substr($phone, 3, 3);
		$thirdGroup = substr($phone, 6, 4);
		return ("($areaCode) $secondGroup-$thirdGroup");
	}
	
}