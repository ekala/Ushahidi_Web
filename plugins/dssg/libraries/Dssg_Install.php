<?php defined('SYSPATH') or die('No direct script access');

class Dssg_Install {
	
	/**
	 * Installs the DSSG plugin
	 */
	public function run_install()
	{
		// Create DB entrie for the dssg_api_url setting
		// TODO: Default value for the DSSG API 
		Settings_Model::save_setting('dssg_api_url', NULL);
	}
	
	public function uninstall()
	{
		// Delete the settings values
		Settings_Model::delete_setting('dssg_api_url');
	}
}