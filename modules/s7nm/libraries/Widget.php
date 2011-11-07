<?php defined('SYSPATH') OR die('No direct access allowed.');

class Widget_Core {

	protected $config = array();

	public function __construct($config = array())
	{
		$this->initialize($config);
	}

	public static function factory($widget, $config = array())
	{
		$class_name = ucfirst(strtolower($widget)).'_Widget';

		if (class_exists($class_name))
			return new $class_name($config);

		if ($file = Kohana::find_file('widgets', $widget))
		{
			require $file;

			if (class_exists($class_name))
				return new $class_name($config);
		}

		throw new Kohana_Exception('core.resource_not_found', 'Widget', $widget);
	}

	public function __toString()
	{
		return $this->render();
	}

	public function initialize($config = array())
	{
		$this->config = $config;
	}

}
