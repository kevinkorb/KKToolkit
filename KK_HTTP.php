<?php
/**
 * These are just wrapper functions to make cURL
 * and other HTTP functions quick and easy to work
 * with.
 *
 */
class KK_HTTP {
	
	/**
	 * A Wrapper Function For Curl Post
	 *
	 * @param string $url
	 * @param array $data
	 * @param array $optional_headers
	 * @return string
	 */
	public static function post($url, $data, $optional_headers = null) {
		
		$o="";
		foreach ($data as $k=>$v) {
			$o.= "$k=".utf8_encode($v)."&";
		}
		$post_data=substr($o,0,-1);
		
		$ch = curl_init();
		$endpoint = $url;
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;

	}
	
	/**
	 * A Wrapper Function For Curl Delete
	 *
	 * @param string $url
	 * @param array $data
	 * @param array $optional_headers
	 * @return string
	 */
	public static function delete($url, $data, $optional_headers = null) {
		
		$o="";
		foreach ($data as $k=>$v) {
			$o.= "$k=".utf8_encode($v)."&";
		}
		$post_data=substr($o,0,-1);
		
		$ch = curl_init();
		$endpoint = $url;
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$response = curl_exec($ch);
		
		$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $status;

	}
	
	/**
	 * Simaliar to file_get_contents but uses cURL
	 * instead of the standard PHP Streams.
	 *
	 * @param string $url
	 * @return string
	 */
	public static function get_contents($url, $connectTimeout = 2, $totalTimout = 5)
	{
	   $ch = curl_init();
	
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $totalTimout);
		ob_start();
	
		curl_exec ($ch);
		curl_close ($ch);
		$string = ob_get_contents();
		
		ob_end_clean();
		return $string;   
	}

	/**
	 * Simaliar to file_get_contents but uses cURL
	 * instead of the standard PHP Streams.
	 *
	 * @param string $url
	 * @return string
	 */
	public static function put_contents($url, $connectTimeout = 2, $totalTimout = 5)
	{
		$ch = curl_init();

		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $totalTimout);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		ob_start();

		curl_exec ($ch);
		curl_close ($ch);
		$string = ob_get_contents();

		ob_end_clean();
		return $string;
	}
	
	/**
	 * Get Secure Contents
	 * 
	 * This just connects using the SSL url and sends the username/password provided.
	 *
	 * @param string $ssl_url
	 * @param string $username
	 * @param string $password
	 * @return string
	 */
	public static function get_secure_contents($ssl_url, $username = false, $password = false)
	{
	   $ch = curl_init();
	
	   curl_setopt ($ch, CURLOPT_URL, $ssl_url);
	   if($username !== false && $password !== false) {
	   		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	   }
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	   curl_setopt ($ch, CURLOPT_HEADER, 0);
	
	   ob_start();
	
	   if(curl_exec ($ch) === false) {
	   		echo "Curl Failed";
	   }
	   echo curl_error($ch);
	   curl_close ($ch);
	   $string = ob_get_contents();
	
	   ob_end_clean();
	   return $string;   
	}
	
}
?>