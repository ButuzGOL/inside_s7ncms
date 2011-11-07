<?php defined('SYSPATH') OR die('No direct access allowed.');

class theme_Core {
	
	public static $name = 'default';
	public static $view;
	public static $css;
	public static $images;
	public static $js;
	public static $is_admin;
	
	static function load_themes()
	{
		$modules = Kohana::config('core.modules');
		array_unshift($modules, THEMEPATH);
		array_unshift($modules, ADMINTHEMEPATH);
		Kohana::config_set('core.modules', $modules);
		
		$is_admin = '';
		if (strpos(Router::$current_uri, 'admin') === 0)
		    $is_admin = 'admin';
	    
	    theme::$name = config::get('s7nm.'.$is_admin.'theme');
	    theme::$view = '../'.$is_admin.'themes/'.theme::$name.'/views';
	    theme::$is_admin = $is_admin;
	    theme::$css = $is_admin.'themes/'.theme::$name.'/css';
	    theme::$images = $is_admin.'themes/'.theme::$name.'/images';
	    theme::$js = $is_admin.'themes/'.theme::$name.'/js';
	}
	
	static function available($theme_path=THEMEPATH)
	{
		$themes = array();
		if ($dh = opendir($theme_path))
		{
			while(($theme = readdir($dh)) !== FALSE)
			{
				$path = $theme_path.$theme.'/theme.xml';
				if (is_file($path))
				{
					$xml = simplexml_load_file($path);
					$themes[$theme] = (string) $xml->name;
				}
			}
		}
		
		return $themes;
	}
}
