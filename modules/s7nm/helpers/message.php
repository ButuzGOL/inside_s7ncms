<?php defined('SYSPATH') OR die('No direct access allowed.');

class message_Core {

	public static function info($message = NULL, $uri = NULL)
	{
		if ($message !== NULL AND $uri !== NULL)
		{
			Session::instance()->set_flash('info_message', $message);
			url::redirect($uri);
		}
		
		return Session::instance()->get('info_message', FALSE);
	}
	
	public static function error($message = NULL, $uri = NULL)
	{
		if ($message !== NULL AND $uri !== NULL)
		{
			Session::instance()->set_flash('error_message', $message);
			url::redirect($uri);
		}
		
		return Session::instance()->get('error_message', FALSE);
	}
	
}
