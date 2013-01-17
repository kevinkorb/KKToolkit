<?php
/**
 * Debug
 *
 * @author Kevin Korb
 */
Class KK_Debug {
	
	/**
	 * Output Array
	 * 
	 * A wrapper function for print_r that provides our
	 * <pre></pre> tags because the three lines just to 
	 * output our array in a nice fashion is just painful.
	 *
	 * @param array $array
	 */
	static public function output_array($array) {
		if(defined('KK_DEBUG_OFF') && KK_DEBUG_OFF == true) {
			return;
		}
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	
    public static function output_yii_models($model_or_array) {
        if(is_object($model_or_array)) {
            self::output_array($model_or_array->getAttributes());
        }
        else {
            $array = array();
            foreach($model_or_array AS $model) {
                $array[] = $model->getAttributes();
            }
            self::output_array($array);
        }
    }

}