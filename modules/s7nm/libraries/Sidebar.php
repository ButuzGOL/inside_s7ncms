<?php defined('SYSPATH') OR die('No direct access allowed.');

class Sidebar_Core {

	private static $instance;

	protected $widgets = array();

	public function __construct()
	{
		self::$instance = $this;
	}

	public static function instance()
	{
		if (self::$instance == NULL)
			new Sidebar;

		return self::$instance;
	}

	public function add($widget, $config = NULL)
	{
		$this->widgets[] = array
		(
			'name' => $widget,
			'config' => $config
		);
	}

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

	public function render()
	{
		$output = '';
		foreach ($this->widgets as $widget)
		{
			if (is_string($widget['name']))
			{
				$output .= Widget::factory($widget['name'], $widget['config'])->render();
			}
			elseif (is_object($widget['name']))
			{
				$output .= $widget['name']->render();
			}
		}

		return $output;
	}

}
