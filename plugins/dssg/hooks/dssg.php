<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Hook class for the DSSG plugin
 */
class dssg {
	
	/**
	 * DSSG_Api object
	 * @var DSSG_Api
	 */
	private $_dssg_api = NULL;
	
	public function __construct()
	{
		// Load the API url
		$api_url = Settings_Model::get_setting('dssg_api_url');
		
		// Check if the URL is valid
		if (valid::url($api_url))
		{
			// Initialize the API instance and initiate event subscription
			$this->_dssg_api = DSSG_Api::instance($api_url);
			Event::add('system.pre_controller', array($this, 'add'));
		}
		else
		{
			// Log the error
			Kohana::log('error', sprintf('An invalid API url has been registered - %s', $api_url));
		}
		
	}
	
	/**
	 * Subscribes callbacks to events
	 */
	public function add()
	{
		// Register plugin hooks
		
		// When a report is being viewed/edited
		Event::add('ushahidi_action.header_scripts', array($this, 'add_header_scripts'));
		Event::add('ushahidi_action.report_display_media', array($this, 'suggest_language'));
		Event::add('ushahidi_action.report_display_media', array($this, 'suggest_entities'));
		
		// When a message has been opened
		Event::add('', array($this, 'similar_messages'));
	}
	
	/**
	 * JavaScript and CSS for the DSSG plugin
	 */
	public function add_header_scripts()
	{
		View::factory('media/css/dssg')->render(TRUE);
	}
	
	/**
	 * Gets the possible languages that a report is in
	 */
	public function suggest_language()
	{
		$incident_id = Event::$data;
		
		$report_description = ORM::factory('incident', $incident_id)->incident_description;
		
		// TODO: Check for cached language suggestions for this report
		$response = $this->_dssg_api->language($report_description);
		if ( ! empty($response))
		{
			list($confidence, $language) = array_values($response);
		
			// TODO: Store the language locally
		
			// Display the suggested language
			View::factory('reports/language_suggest')
				->bind('language', $language)
				->bind('confidence', $confidence)
				->render(TRUE);
		}
		
	}
	
	/**
	 * Gets the entities contained in the report
	 */
	public function suggest_entities()
	{
		$incident_id = Event::$data;
		$report_description = ORM::factory('incident', $incident_id)->incident_description;
		
		// Get the entities
		$response = $this->_dssg_api->entities($report_description);
		if ( ! empty($response))
		{
			$entities = $response['entities'];

			View::factory('reports/entity_suggest')
				->bind('entities', $entities)
				->render(TRUE);
		}
	}
}

new dssg();