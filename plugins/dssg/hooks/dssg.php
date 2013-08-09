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
		
		// Frontend
		// When a report is being viewed/edited
		Event::add('ushahidi_action.header_scripts', array($this, 'add_header_scripts'));
		Event::add('ushahidi_action.report_display_media', array($this, 'report_metadata'));
		
		// TODO: Admin console events
		// Event::add('', array($this, 'report_metadata_admin'));
		// Event::add('', array($this, 'similar_messages'));
		
		// When a message has been opened
		// Event::add('', array($this, 'similar_messages'));
		
		// When a new report is created
		Event::add('ushahidi_action.report_new', array($this, 'add_report'));
		
		// When a new message is created
		Event::add('ushahidi_action.message_new', array($this, 'add_message'));
	}
	
	/**
	 * JavaScript and CSS for the DSSG plugin
	 */
	public function add_header_scripts()
	{
		View::factory('media/css/dssg')->render(TRUE);
	}
	
	/**
	 * Displays the report metadata - language, location and entities
	 */
	public function report_metadata()
	{
		$incident_id = Event::$data;
		
		$report_description = ORM::factory('incident', $incident_id)->incident_description;
		
		list($language, $tags, $locations) = $this->_extract_metadata($report_description);
		
		View::factory('reports/metadata')
			->bind('language', $language)
			->bind('entities', $tags)
			->bind('locations', $locations)
			->render(TRUE);
	}
	/**
	 * Extracts the report metadata - language, entities and locations
	 */
	private function _extract_metadata($report_description)
	{
		// TODO: Check for cached language suggestions for this report
		
		$language = $this->_dssg_api->language($report_description);
		$tags = $this->_get_tags($report_description);
		$locations = $this->_get_locations($report_description);
		
		return array($language, $tags, $locations);
	}
	
	/**
	 * Gets the entities contained in the report
	 *
	 * @param  string  report_description
	 * @return array   List of people and organization names
	 */
	private function _get_tags($report_description)
	{
		// Get the entities
		$entity_response = $this->_dssg_api->entities($report_description);
		$tags  = array();

		if ( ! empty($entity_response))
		{
			$tags = array();
			foreach ($entity_response['entities'] as $type => $entities)
			{
				$tags = array_merge($tags, $entities);
			}
		}
		
		return $tags;
	}
	
	/**
	 * Returns the names of locations contained in the specified text
	 *
	 * @param   string  report_description
	 * @param   array   List of location names
	 */
	private function _get_locations($report_description)
	{
		$locations_response= $this->_dssg_api->locations($report_description);
		
		$locations = array();
		if ( ! empty($locations_response))
		{
			foreach ($locations_response['locations'] as $type => $values)
			{
				$locations = array_merge($locations, $values);
			}
		}
		
		return $locations;
	}
	
	/**
	 * This method is called when a new report is created via 
	 * Incident_Model::save()
	 */
	public function add_report()
	{
		$incident = Event::$data;

		Kohana::log('info', sprintf('Posting report %d to the API', $incident->id));
		$this->_dssg_api->add_report($incident->id, $incident->incident_title, $incident->incident_description);
	}
	
	/**
	 * This method is called when a new message is saved to the database
	 * via Message_Model::save()
	 */
	public function add_message()
	{
		$message = Event::$data;
		Kohana::log('info', sprintf('Posting message % to the API', $message->id));
		$this->_dssg_api->add_message($message->id, $message->message);
	}
}

new dssg();