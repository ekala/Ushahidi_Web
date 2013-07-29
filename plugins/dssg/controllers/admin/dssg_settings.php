<?php defined('SYSPATH') or die('No direct script access');
/**
 * DSSG settings controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @category   plugins
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @subpackage DSSG
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 * 
 */
class Dssg_Settings_Controller extends Admin_Controller {
	
	public function index()
	{
		$this->template->bind('content', $settings_view);

		$settings_view = View::factory('admin/dssg/settings')
			->bind('form', $form)
			->bind('form_error', $form_error)
			->bind('form_saved', $form_saved);
		
		$form =  array('dssg_api_url' => '');
		$form_error = FALSE;
		$form_saved = FALSE;
		
		if (request::method() == 'post')
		{
			// Validation
			
			// Register the deployment
			
			// Save the setting
			
			$form_saved = TRUE;
		}
	}
	
}