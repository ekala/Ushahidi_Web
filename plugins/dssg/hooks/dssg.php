<?php defined('SYSPATH') or die('No direct script access.');

class dssg {
	
	public function __construct()
	{
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	public function add()
	{
		// Register plugin hooks
	}
}

new dssg();