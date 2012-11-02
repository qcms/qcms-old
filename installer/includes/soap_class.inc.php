<?
//=============================================================================
// Определения классов и функций работы SOAP


//-----------------------------------------------------------------------------
// Soap base class
class CSoapBaseClass {
	//---------------------------------------------------------------------------
	// function checks code 
	function CheckCode($code) {
		$ret = false;
		if ($code && $code == SOAP_CLASS_CODE) {
			$ret = true;
		}
		return $ret;
	}
}

//-----------------------------------------------------------------------------
// SOAP client class
class CSoapClientClass extends CSoapBaseClass {
	/*
	var $code;
	
	//---------------------------------------------------------------------------
	// constructor
	function CSoapClientClass($code = SOAP_CLASS_CODE)
	{
		$this->code = $code;
	}
	*/
	
	//---------------------------------------------------------------------------
	// function sends email message
	function SendMessage($url, $code, $from_email, $from_name, $to_email, $subject, $message) {
		$ret = false;
		
		$fields = array ('code' => $code, 'from_email' => $from_email, 'from_name' => $from_name, 'to_email' => $to_email, 'subject' => $subject, 'message' => $message );
		
		//$response = http_post_fields ( $url, $fields );
		$cc = new CSoapCurlClass ( );
		//$cc->get ( 'http://www.example.com' );
		$data = http_build_query($fields); 
		$response = $cc->post( $url, $data);
		
		if ($response && $response == "1") 
			$ret = true;
		
		return $ret;
	}
	
//	//-----------------------------------------------------------------------------
//	// function gets url and returns its content
//	function soap_scrapping_url($url) {
//		global $file_content;
//		$file_content = "";
//		
//		$ch = curl_init ( $url );
//		
//		curl_setopt ( $ch, CURLOPT_HEADER, $header );
//		curl_setopt ( $ch, CURLOPT_WRITEFUNCTION, "CSoapClientClass::soap_scrapping_url_ex" );
//		//curl_setopt($ch, CURLOPT_FILE, $fp);
//		
//
//		curl_exec ( $ch );
//		
//		curl_close ( $ch );
//		//fclose($fp);
//		
//
//		unset ( $ch );
//		
//		// TODO: debugging
//		if (DEBUG) {
//			global $file_number;
//			$fp = fopen ( str_replace ( "[N]", $file_number, TEMPFILE_N ), "w" );
//			fwrite ( $fp, $file_content );
//			fclose ( $fp );
//			$file_number ++;
//		}
//		return $file_content;
//	}
//	
//	//-----------------------------------------------------------------------------
//	// backtrace function for getting itunes url
//	function scrapping_url_ex($ch, $str) {
//		// check stop is required
//		if (file_exists ( STOPFILE ))
//			exit ();
//		
//		global $file_content;
//		$file_content .= $str;
//		$ret = strlen ( $str );
//		unset ( $str );
//		return $ret;
//	}
}

//-----------------------------------------------------------------------------
// SOAP server class
class CSoapServerClass extends CSoapBaseClass {
	/*
	var $code;

	//---------------------------------------------------------------------------
	// constructor
	function CSoapServerClass($code= SOAP_CLASS_CODE)
	{
		$this->code = $code;
	}
	*/
	
	//---------------------------------------------------------------------------
	// function sends email message
	function SendMessage($code, $from_email, $from_name, $to_email, $subject, $message) {
		$ret = false;
		if (CSoapBaseClass::CheckCode ( $code )) {
			$ret = send_message ( $from_email, $from_name, $to_email, $subject, $message );
		}
		
		return $ret;
	}
}

class CSoapCurlClass {
	var $headers;
	var $user_agent;
	var $compression;
	var $cookie_file;
	var $proxy;
	var $cookies;
	
	function cURL($cookies = TRUE, $cookie = 'cookies.txt', $compression = 'gzip', $proxy = '') {
		$this->headers [] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
		$this->headers [] = 'Connection: Keep-Alive';
		$this->headers [] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
		$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
		$this->compression = $compression;
		$this->proxy = $proxy;
		$this->cookies = $cookies;
		if ($this->cookies == TRUE)
			$this->cookie ( $cookie );
	}
	function cookie($cookie_file) {
		if (file_exists ( $cookie_file )) {
			$this->cookie_file = $cookie_file;
		} else {
			fopen ( $cookie_file, 'w' ) or $this->error ( 'The cookie file could not be opened. Make sure this directory has the correct permissions' );
			$this->cookie_file = $cookie_file;
			fclose ( $this->cookie_file );
		}
	}
	function get($url) {
		$process = curl_init ( $url );
		curl_setopt ( $process, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt ( $process, CURLOPT_HEADER, 0 );
		curl_setopt ( $process, CURLOPT_USERAGENT, $this->user_agent );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEFILE, $this->cookie_file );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEJAR, $this->cookie_file );
		curl_setopt ( $process, CURLOPT_ENCODING, $this->compression );
		curl_setopt ( $process, CURLOPT_TIMEOUT, 30 );
		if ($this->proxy)
			curl_setopt ( $process, CURLOPT_PROXY, $this->proxy );
		curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $process, CURLOPT_FOLLOWLOCATION, 1 );
		$return = curl_exec ( $process );
		curl_close ( $process );
		return $return;
	}
	function post($url, $data) {
		$process = curl_init ( $url );
		@curl_setopt ( $process, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt ( $process, CURLOPT_HEADER, 1 );
		curl_setopt ( $process, CURLOPT_USERAGENT, $this->user_agent );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEFILE, $this->cookie_file );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEJAR, $this->cookie_file );
		curl_setopt ( $process, CURLOPT_ENCODING, $this->compression );
		curl_setopt ( $process, CURLOPT_TIMEOUT, 30 );
		if ($this->proxy)
			curl_setopt ( $process, CURLOPT_PROXY, $this->proxy );
		
		curl_setopt ( $process, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $process, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $process, CURLOPT_POST, 1 );
		curl_setopt ( $process, CURLOPT_WRITEFUNCTION, "soap_scrapping_ex");
		
		global $soap_content;
		$soap_content = "";
		$return = curl_exec ( $process );
		
		$return = $soap_content;
		//var_dump($return);
		
		curl_close ( $process );
		return $return;
	}
	function error($error) {
		echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
		die ();
	}
}

function soap_scrapping_ex($ch, $str)
{
	global $soap_content;
	$soap_content .= $str;
	$ret = strlen($str);
	unset($str);
	return $ret;
}
?>
