<?php
/**
 * HTTP client Implementation based on php-curl
 *
 * @author Henry Addo <henry@addhen.org>
 * @version 1.0
 * @package HttpClient
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class HttpClient_Core {
	    
	/**
	 * Holds error messages if error occurs
	 * @var string
	 */
	private $_error_msg;

	/**
	 * HTTP status code return by the curl request
	 * @var int
	 */
	private $_status_code;
	
	/**
	 * curl connection options
	 * @var array
	 */
	private $_options = array();
	
	public function __construct($url = FALSE, $timeout = 20)
	{
		// Default curl options
		$this->_options = array(
			CURLOPT_TIMEOUT => $timeout,
			
			CURLOPT_RETURNTRANSFER => TRUE,
			
			// Set error in case HTTP response code > 300
			CURLOPT_FAILONERROR => TRUE,
			
			// Allow redirects
			CURLOPT_FOLLOWLOCATION => TRUE,
			
			// Use gzip if possible
			CURLOPT_ENCODING => 'gzip,deflate',
			
			// Disable SSL verification
			CURLOPT_SSL_VERIFYPEER => FALSE,
			
			CURLOPT_SSL_VERIFYHOST => 2
		);
		
		// Check if the URL has been set
		if (valid::url($url))
		{
			$this->_options[CURLOPT_URL] = $url;
		}
	}
    
	/**
	 * Set client's user agent
	 *
	 * @access private
	 * @param string useragent
	 */
	public function set_useragent($useragent)
	{
		$this->_options[CURLOPT_USERAGENT] = $useragent;
	}
	
	/**
	 * Get http response code
	 *
	 * @access private
	 * @return int
	 */
	public function get_http_response_code()
	{
		return $this->_status_code;
	}
	
	/**
	 * Set error message that might show up
	 *
	 * @access protected
	 * @param string error_msg - The error message
	 */
	public function get_error_msg()
	{
		return $this->_error_msg;
	}	
    
	/**
	 * Sets the error message
	 *
	 * @param  string $error curl error message
	 */
	private function _set_error_msg($error)
	{
		$this->_error_msg  = sprintf("Error fetching remote url [status %s]: %s",
			$this->_status_code, $error);
	}
	
	/** 
	 * Fetch data from target URL return data returned from url or 
	 * false if error occured
	 *
	 * @param   string uri          URI of the request
	 * @param   array  parameters   Array of parameters to be submitted
	 * @param   string http_method  HTTP request method
	 * @param   array  headers      Header information
	 * @return  string If successful, FALSE otherwise
	 */
	public function execute($uri = FALSE, $parameters = array(), $http_method = "GET", $headers = array())
	{
		// Check if the URL has been specified
		if ( ! isset($this->_options[CURLOPT_URL]) AND $uri === FALSE)
		{
			throw new Kohana_Exception("The URI of the request has not been specified");
		}
		
		// Validate and set the URI of the request
		if ($uri AND valid::url($uri))
		{
			$this->_options[CURLOPT_URL] = $uri;
		}
		else
		{
			$message = sprintf("The specified uri '%s' is invalid");
			throw new Kohana_Exception($message);
		}
		
		if ( ! empty($http_method))
		{
			$this->_options[CURLOPT_CUSTOMREQUEST] = strtoupper($http_method);
		}

		// Check for the request method
		switch (strtoupper($http_method))
		{
			case "POST":
				$this->_options[CURLOPT_POST] = TRUE;
				// Do not break
			case "PUT":
			case "PATCH":
				if (is_array($parameters) AND ! empty($parameters))
				{
					$this->_options[CURLOPT_POSTFIELDS] = http_build_query($parameters, NULL, "&");
				}
				break;
				
			case "GET":
			case "DELETE":
				if ( ! empty($parameters))
				{
					$url = $this->_options[CURLOPT_URL].'?'.http_build_query($parameters, NULL, '&');;
					$this->_options[CURLOPT_URL] = $url;
				}
				break;
			default:
				break;
		}
		
		// Headers
		if (is_array($headers) AND ! empty($headers))
		{
			$header = array();
			foreach ($headers as $param => $value)
			{
				$header[] = sprintf("%s: %s", $param, $value);
			}
			$this->_options[CURLOPT_HTTPHEADER] = $header;
		}
		
		// Open remote connection
		$curl = curl_init();
		
		// Set connection options
		curl_setopt_array($curl, $this->_options);
		
		// Get the response body
		$response = curl_exec($curl);
		
		$this->_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($response === FALSE)
		{
			$error = curl_error($curl);
		}
		
		if (isset($error))
		{
			$this->_set_error_msg($error);
			return FALSE;
		}
		
		// Close the connection
		curl_close($curl);
		
		return $response;
	}

}

?>
