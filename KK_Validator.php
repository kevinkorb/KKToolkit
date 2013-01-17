<?php
class KK_Validator {
	
	protected $current_field_name;
	protected $current_field_value;
	protected $current_error_message_field;
	protected $validation_error_array = array();
	
	/**
	 * Set Field
	 *
	 * @param string $field_name
	 * @param string $field_value
	 * @param boolean $trim
	 * @return KK_Validator object
	 */
	public function setField($field_name, $field_value, $required = true, $trim = true, $error_message_field_display = false) {
		$this->current_error_message_field = ($error_message_field_display === false) ? KK_Filter::field_to_text($field_name) : $error_message_field_display;
		$this->current_field_name = $field_name;
		if($trim === true) {
			$this->current_field_value = trim($field_value);
		}
		else {
			$this->current_field_value = $field_value;
		}
		if($required === true) {
			return $this->addRequired();
		}
		else {
			return $this;
		}
		
	}
	
	/**
	 * Add Required
	 * 
	 * Notice that this is called from the setField method.
	 *
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addRequired($error_message_override = false) {
		if($this->current_field_value == '') {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field is Required";
			}
		}
		return $this;
	}
	
	/**
	 * Add Minimum Characters
	 *
	 * @param int $min
	 * @return KK_Validator object
	 */
	public function addMin($min, $error_message_override = false) {
		if(strlen($this->current_field_value) < $min) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must be at least $min characters long.";
			}
		}
		return $this;
	}
	
	/**
	 * Add Maximum Characters
	 *
	 * @param int $max
	 * @return KK_Validator object
	 */
	public function addMax($max, $error_message_override = false) {
		if(strlen($this->current_field_value) > $max) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;	
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must be less than $max characters long.";
			}
		}
		return $this;
	}
	
	/**
	 * Add AlphaOnly Validation
	 * 
	 * @return KK_Validator object
	 */
	public function addAlphaOnly($error_message_override = false) {
		$pattern = "/^[A-z ]+$/";
		if(!preg_match($pattern, $this->current_field_value)) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must only contain letters.";
			}
		}
		return $this;
	}
	
	/**
	 * Add Numeric Only Validation
	 *
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addNumericOnly($error_message_override = false) {
		$pattern = "/^[0-9]+$/";
		if(!preg_match($pattern, $this->current_field_value)) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must be numbers only.";
			}
		}
		return $this;
	}
	
	/**
	 * Add Email Address Validation
	 *
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addEmail($error_message_override = false) {
		if(!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $this->current_field_value)) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must be a valid e-mail address.";
			}
		}
		return $this;
	}
	
	/**
	 * Add Phone Number Validation
	 *
	 * @param string $filteredValue 
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addPhone(&$filteredValue= false, $error_message_override = false) {
		$pattern = "/[^0-9]/";
		$result = preg_replace($pattern, "", $this->current_field_value);
		$this->current_field_value = $result;

		if(strlen($result) != 10) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must contain 10 numbers.";
			}			
		}
		else {
			if($filteredValue !== false) {
				$filteredValue = substr($result, 0, 3)."-".substr($result, 3, 3)."-".substr($result, 6, 4);
			}
		}

		return $this;	
	}

	/**
	 * Add Must Equal Value Validation
	 *
	 * @param string $value
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addMustEqual($value, $error_message_override = false) {
		if($this->current_field_value != $value) {
			if($error_message_override !== false) {
				$this->validation_error_array[$this->current_field_name] = $error_message_override;
			}
			else {
				$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field Must Equal '$value'";
			}
		}
		return $this;
	}
	
	/**
	 * Add Max Value Validation
	 *
	 * @param int $value
	 * @param boolean $equal_value_validates
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addMaxValue($value, $equal_value_validates = true, $error_message_override = false) {
		if($equal_value_validates) {
			if(!$this->current_field_value <= $value) {
				if($error_message_override !== false) {
					$this->validation_error_array[$this->current_field_name] = $error_message_override;
				}
				else {
					$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must me less than or equal to $value";
				}
			}
		}
		else {
			if(!$this->current_field_value < $value) {
				if($error_message_override !== false) {
					$this->validation_error_array[$this->current_field_name] = $error_message_override;
				}
				else {
					$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must me less than $value";
				}
			}			
		}
		return $this;
	}
	
	/**
	 * Add Minimum Value Validation
	 *
	 * @param int $value
	 * @param boolean $equal_value_validates
	 * @param string $error_message_override
	 * @return KK_Validator
	 */
	public function addMinValue($value, $equal_value_validates = true, $error_message_override = false) {
		if($equal_value_validates) {
			if(!$this->current_field_value >= $value) {
				if($error_message_override !== false) {
					$this->validation_error_array[$this->current_field_name] = $error_message_override;
				}
				else {
					$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must me greater than or equal to $value";
				}
			}
		}
		else {
			if(!$this->current_field_value > $value) {
				if($error_message_override !== false) {
					$this->validation_error_array[$this->current_field_name] = $error_message_override;
				}
				else {
					$this->validation_error_array[$this->current_field_name] = "$this->current_error_message_field must me greater than $value";
				}
			}			
		}
		return $this;
	}
	
	/**
	 * Get Error Array
	 * 
	 * If There Are No Errors, it will return false.
	 * 
	 * Otherwise it will return an array where the keys are the field names,
	 * and the values are the validation messages.
	 *
	 * @return boolean or array
	 */
	public function getErrorArray() {
		if(count($this->validation_error_array) == 0) {
			return false;
		}
		return $this->validation_error_array;
	}
	
}
?>