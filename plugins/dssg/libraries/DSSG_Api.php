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
	private function __construct()
	{
		// Get the DSSG API url and assigned deployment id
		$this->_api_url = Settings_Model::get_setting('dssg_api_url');
		$this->_deployment_id = Settings_Model::get_setting('dssg_deployment_id');
		
		// Initialize the HttpClient
		$this->_http_client = new HttpClient();
	}

	/**
	 * Returns a singleton instance of the DSSG_API object
	 *
	 * @return DSSG_API
	 */
	public static function & instance()
	{
		if (empty(self::_instance))
		{
			self::_instance = new DSSG_Api();
		}
		
		return self::_instance;
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
	public function register()
	{
		// Fetch the categories
		// Fields for each entry: id, name, children (id, name)
		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', 1)->find_all() as $category)
		{
			$entry = array(
				'id' => $category->id,
				'name'=> $category->category_title
			);
			
			if ($category->parent_id > 0)
			{
				// Add child to parent
				$this->_add_child($categories, $category);
			}

			$categories[] = $entry;
		}

		// Request parameters
		$parameters = array(
			// Name of the deployment
			'name' => Settings_Model::get_setting('site_name'),
			
			// Categories in the deployment
			'categories' => $categories
		);

		// Send request to register deployment
		$response = $this->_post("/deployments", $parameters);
		
		// Save returned deployment ID in the settings table
		Settings_Model::save_setting('dssg_deployment_id', $response['deployment_id']);
	}
	
	/**
	 * Adds the child category specified in $category to
	 * its respective parent in the $categories array
	 *
	 * @param array categories
	 * @param mixed category
	 */
	private function _add_child($categories, $category)
	{
		foreach ($category as & $entry)
		{
			if ($entry['id'] === $category->parent_id)
			{
				if ( ! array_key_exists('children', $entry))
				{
					$entry['children'] = array();
				}
				
				// Add child entry
				$entry['children'][] = array(
					'id' => $category->id,
					'name' => $category->category_title
				);
				
				break;
			}
		}
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
	
	public function locations($text)
	{
		$parameters = array("text" => $text);
		return $this->_post("/locations", $parameters);
	}
	
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
			
		return $response;
	}
	
	/**
	 * Sends a HTTP GET request to the specified endpoint
	 */
	private function _get($endpoint, $parameters)
	{
		$request_uri = $this->_api_url.$endpoint;
		return $this->_http_client->execute($reqeust_uri, $parameters);
	}

	/**
	 * Sends a HTTP DELETE request to the specified $endpoint
	 */
	private function _delete($endpoint)
	{
		$request_uri = $this->_api_url.$endpoint;
		$this->_http_client->execute($reqeust_uri, NULL, "DELETE");
	}
	
	/**
	 * Sends a HTTP PUT request to the specified $endpoint
	 */
	private function _put($endpoint, $parameters)
	{
		$request_uri = $this->_api_url.$endpoint;
		$headers = array("Content-Type" => "application/json;charset=utf-8");
		
		return $this->_http_client->execute($request_uri, json_encode($parameters), "PUT", $headers);
	}
}