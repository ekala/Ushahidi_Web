<?php defined('SYSPATH') or die('No direct script access');
/**
 * This is a wrapper class for communicating with the DSSG
 * API via HTTP. All data is sent and received as JSON
 *
 */
class DSSG_Api_Core {

	/**
	 * @var DSSG_API
	 * An instance of the DSSG API
	 */
	private static $_instance = NULL;
	
	/**
	 * @var string
	 * The DSSG API endpoint
	 */
	private $_api_url;
	
	/**
	 * @var HttpClient
	 */
	private $_http_client;
	
	/**
	 * The ID of the current deployment on the DSSG application
	 * @var mixed
	 */
	private $_deployment_id;
	
	/**
	 * Private constructor
	 */
	private function __construct($api_url = NULL)
	{
		// Initialize attributes
		$this->api_url($api_url);
		$this->_deployment_id = Settings_Model::get_setting('dssg_deployment_id');
		
		// Initialize the HttpClient
		$this->_http_client = new HttpClient();
	}

	/**
	 * Returns a singleton instance of the DSSG_API object
	 *
	 * @return DSSG_API
	 */
	public static function & instance($api_url = NULL)
	{
		if (empty(self::$_instance))
		{
			self::$_instance = new DSSG_Api($api_url);
		}
		
		return self::$_instance;
	}
	
	/**
	 * Sets and gets the api url
	 *
	 * @param    string  api_url  The base url of the DSSG application
	 */
	public function api_url($api_url = NULL)
	{
		if ( ! empty($api_url) AND valid::url($api_url))
		{
			$this->_api_url = $api_url;
		}
		else
		{
			if (empty($this->_api_url))
			{
				$this->_api_url = Settings_Model::get_setting('dssg_api_url');
			}
			return $this->_api_url;
		}
	}
	
	/**
	 * Sets and gets the HttpClient to be used for performing external requests
	 * If no value is specified, current HttpClient object is returned
	 *
	 * @param  HttpClient http_client
	 */
	public function http_client($http_client = NULL)
	{
		if ( ! empty($http_client) AND $http_client instanceof HttpClient)
		{
			$this->_http_client = $http_client;
		}
		else
		{
			return $this->_http_client;
		}
	}

	/**
	 * Registers the current deployment with DSSG API
	 * During registration, the following information is submitted:
	 *	- Name and URL of the deployment
	 *	- Categories (parent and child)
	 *
	 * Upon successful registration, the API responds with the ID
	 * that has been assigned to the deployment. This ID is used for
	 * submitting deployment-specific requests such as:
	 * 	- Suggested report categories
	 *	- Suggested similar messages/reports
	 */
	public function register_deployment($api_url)
	{
		// Check if the specified uri has already been registered
		// Prevent double registration of the same API url
		$current_url = trim(Settings_Model::get_setting('dssg_api_url'));
		if (md5(strtolower($current_url)) === md5(strtolower($api_url)))
			return;

		// Fetch the categories
		// Fields for each entry: id, name, children (id, name)
		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', 1)->find_all() as $category)
		{
			$categories[] = array(
				'origin_category_id' => $category->id,
				'origin_parent_id' => $category->parent_id,
				'title'=> $category->category_title
			);
		}

		// Request parameters
		$parameters = array(
			// Name of the deployment
			'name' => Settings_Model::get_setting('site_name'),
			
			// URL of this deployment
			'url' => url::base(TRUE, TRUE),
			
			// Categories in the deployment
			'categories' => $categories
		);
		
		// Set $_api_url
		$this->_api_url = $api_url;

		// Send request to register deployment
		$response = $this->_post("/deployments", $parameters);
		
		// TODO: Only save if a 200 status and deployment id have been
		// returned
		// Save plugin settings
		Settings_Model::save_setting('dssg_api_url', $api_url);
		Settings_Model::save_setting('dssg_deployment_id', $response['id']);
	}

	/**
	 * Gets messages that are similar to the specified text
	 * @param  string $text
	 */
	public function similar_messages($text)
	{
		$endpoint = "/deployments/".$this->_deployment_id."/similar";
		$parameters = array("text" => $text);
		return $this->_post($endpoint, $parameters);
	}
	
	/**
	 * Finds and returns any personally identifiable
	 * information in the provided text
	 *
	 * @param  string text The text to be analysed
	 */
	public function personal_info($text)
	{
		$parameters = array("text" => $text);
		return $this->_post("/private_info", $parameters);
	}
	
	/**
	 * Returns the list of possible location names contained
	 * in the provided $text
	 *
	 * @param  string text
	 */
	public function locations($text)
	{
		$parameters = array("text" => $text);
		return $this->_post("/locations", $parameters);
	}
	
	/**
	 * Returns the list of other entities - other than location -
	 * e.g. people, organisations, events etc contained in
	 * the provided $text
	 */
	public function entities($text)
	{
		$parameters = array("text" => $text);
		return $this->_post("/entities", $parameters);
	}
	
	/**
	 * Gets the natural language(s) that the provided $text is
	 * in by sending a POST language
	 *
	 * @param   string  $text
	 * @return  array   A list of one of more languages, FALSE otherwise
	 */
	public function language($text)
	{
		$parameters = array("text" => $text);
		return $this->_post("/language", $parameters);
	}
	
	/**
	 * Sends a HTTP POST to the specified $endpoint
	 */
	private function _post($endpoint, $parameters = array())
	{
		$request_uri = $this->_api_url.$endpoint;
		$headers = array("Content-Type" => "application/json;charset=utf-8");
		
		$response = $this->_http_client->execute($request_uri,
			json_encode($parameters), "POST", $headers);
			
		return $this->_decode_response($response);
	}
	
	/**
	 * Sends a HTTP GET request
	 *
	 * @param  string   endpoint
	 * @param  array    parameters
	 * @return array
	 */
	private function _get($endpoint, $parameters = array())
	{
		$request_uri = $this->_api_url.$endpoint;
		$response = $this->_http_client->execute($reqeust_uri, $parameters);
		
		return $this->_decode_response($response);
	}

	/**
	 * Sends a HTTP DELETE request
	 *
	 * @param  string   endpoint
	 * @param  array    parameters
	 * @return array
	 */
	private function _delete($endpoint)
	{
		$request_uri = $this->_api_url.$endpoint;
		$response = $this->_http_client->execute($reqeust_uri, NULL, "DELETE");
		
		return $this->_decode_response($response);
	}
	
	/**
	 * Sends a HTTP PUT request
	 *
	 * @param  string   endpoint
	 * @param  array    parameters
	 * @return array
	 */
	private function _put($endpoint, $parameters = array())
	{
		$request_uri = $this->_api_url.$endpoint;
		$headers = array("Content-Type" => "application/json;charset=utf-8");
		
		$response = $this->_http_client->execute($request_uri, json_encode($parameters), "PUT", $headers);
		
		return $this->_decode_response($response);
	}
	
	/**
	 * Sends a HTTP PATCH request
	 *
	 * @param   string  endpoint
	 * @param   array   parameter  Request parameters
	 * @return  array
	 */
	private function _patch($endpint, $parameters)
	{
		$request_uri = $this->_api_url.$endpoint;
		$headers = array("Content-Type" => "application/json;charset=utf-8");
		
		$response = $this->_http_client->execute($request_uri, json_encode($parameters), "PATCH", $headers);
		
		return $this->_decode_response($response);
	}
	
	/**
	 * Decodes the given $response as a JSON object and returns the resulting array. If
	 * the decoding fails, the response is returned as is
	 *
	 * @param   string  response
	 * @return  mixed   Array if the JSON response is successfully decoded, string otherwise
	 */
	private function _decode_response($response)
	{
		try
		{
			$json = json_decode($response, TRUE);
		}
		catch (Exception $e)
		{
			$json = array();
			Kohana::log('error', $e->getMessage());
		}
		
		return ($json === NULL) ? $response : $json;
	}

	/**
	 * Posts a report to the DSSG API
	 *
	 * @param  Incident_Model incident The incident to be posted
	 * @return array
	 */
	public function add_report($incident)
	{
		$categories = array();
		foreach ($incident->category as $cat)
		{
			$categories[] = $cat->id;
		}

		$parameters = array(
			'origin_report_id' => $incident->id,
			'title' => $incident->title,
			'description' => $incident->description,
			'categories' => $categories
		);
		
		return $this->_post('/reports/'.$this->_deployment_id, $parameters);
	}

	/**
	 * Posts a message to the DSSG API
	 *
	 * @param int    id The database ID of the message
	 * @param string content The content of the message
	 */
	public function add_message($id, $content)
	{
		$parameters = array('origin_message_id' => $id, 'content' => $content);
		return $this->_post('/messages/'.$this->_deployment_id, $parameters);
	}
}