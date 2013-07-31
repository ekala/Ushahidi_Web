<?php defined('SYSPATH') or die('No direct script access');

/**
 * Tests for the DSSG_Api class
 *
 */
class DSSG_Api_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * The DSSG_Api object reference
	 * @var DSSG_Api
	 */
	private $_dssg_api = NULL;
	
	/**
	 * The test text to be used for making API requests
	 * @var string
	 */
	private $_text = "I have been using a zombie blasting gun to destroy as many aas I can. Here in Montana we are mobbed with zombies. There are barely any weapons or ammo left anywhere! If we want to get a weapon we have to go to Farmer Pete's run down gun shop. There are dead bodies and body parts everywhere! It's absolutely crazy here! Zombies just kinda walk in you house these days and it's perfectly normal. Haha! I just blasted one! My friend Rachel just turned into one a month ago. Same with my mom Cherl and my dad Daris. I just killed another zombie! Crazy stuff round these parts. It's just me and my gun and my iPod of course. Power is down and has been for a few months. Telephone lines are down too! I just wanna run away but I can't because the zombies will turn me into one of them! Please help us! Now!";
	
	/**
	 * JSON content type header
	 * @var array
	 */
	private $_json_header = array("Content-Type" => "application/json;charset=utf-8");
	
	public function setUp()
	{
		// Create a DSSG_Api object
		$this->_dssg_api = DSSG_Api::instance('http://annotate.ushahididev.com/v1');

		// Get the base API URI
		$this->_api_url = $this->_dssg_api->api_url();

		// Create a mock for the HttpClient
		$this->_mock_http_client = $this->getMock('HttpClient');
		
		$this->_dssg_api->http_client($this->_mock_http_client);
		
		$this->_text_parameter = json_encode(array('text' => $this->_text));
	}
	
	/**
	 * @covers DSSG_Api::personal_info
	 */
	public function testGetPersonalInfo()
	{
		// Set the expectation for the execute() method to be
		// called only once
		$this->_mock_http_client->expects($this->once())
			->method('execute')
			->with($this->equalTo($this->_api_url."/private_info"),
			       $this->_text_parameter,
			       $this->equalTo("POST"),
			       $this->_json_header);
		   
		$this->_dssg_api->personal_info($this->_text);
	}
	
	/**
	 * @covers DSSG_Api::language
	 */
	public function testGetLanguage()
	{
		$this->_mock_http_client->expects($this->once())
			->method('execute')
			->with($this->equalTo($this->_api_url.'/language'),
			       $this->_text_parameter,
			       $this->equalTo("POST"),
			       $this->_json_header);

		$this->_dssg_api->language($this->_text);
	}
	
	/**
	 * @covers DSSG_Api::locations
	 */
	public function testGetLocations()
	{
		$this->_mock_http_client->expects($this->once())
			->method('execute')
			->with($this->equalTo($this->_api_url.'/locations'),
			       $this->_text_parameter,
			       $this->equalTo("POST"),
			       $this->_json_header);

		$this->_dssg_api->locations($this->_text);
		
	}
	
	/**
	 * @covers DSSG_Api::entities
	 */
	public function testGetEntities()
	{
		$this->_mock_http_client->expects($this->once())
			->method('execute')
			->with($this->equalTo($this->_api_url.'/entities'),
			       $this->_text_parameter,
			       $this->equalTo("POST"),
			       $this->_json_header);

		$this->_dssg_api->entities($this->_text);
	}	
}