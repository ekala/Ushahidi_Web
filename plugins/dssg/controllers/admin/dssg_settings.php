<?php defined('SYSPATH') or die('No direct script access');
/**
 * DSSG settings controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @category   Controllers
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @subpackage DSSG
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 * 
 */
class Dssg_Settings_Controller extends Admin_Controller {
	
	public function index()
	{
		$this->template->set('this_page', 'addons')
			->bind('content', $content);

		$content = View::factory('admin/addons/plugin_settings')
			->set('title', 'DSSG Plugin Settings')
			->bind('settings_form', $settings_form);
		
		$settings_form = View::factory('admin/dssg/settings')
			->bind('form', $form)
			->bind('form_error', $form_error)
			->bind('form_saved', $form_saved);
		
		$form =  array('dssg_api_url' => '');
		$form_error = FALSE;
		$form_saved = FALSE;
		
		$dssg_api = DSSG_Api::instance();
		
		if (request::method() == 'post')
		{
			// Validation
			$api_url = $this->input->post('dssg_api_url');
			if (valid::url($api_url))
			{
				// Register the deployment
				$dssg_api->register_deployment($api_url);
				$form_saved = TRUE;
				$form['dssg_api_url'] = $api_url;
			}
			else
			{
				$form_error = TRUE;
			}
		}
		else
		{
			$form['dssg_api_url'] = Settings_Model::get_setting('dssg_api_url');
		}
	}
	
}