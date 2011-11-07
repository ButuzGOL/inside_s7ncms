<?php defined('SYSPATH') OR die('No direct access allowed.');

class View extends View_Core {

	public function set_filename($name, $type = NULL)
	{
		if ($path = Kohana::find_file(theme::$view, $name) OR
		    $path = Kohana::find_file(theme::$is_admin.'themes/'.theme::$name.'/views', $name))
		{
			$this->kohana_filename = $path;
			$this->kohana_filetype = EXT;
		}
		else
		{
			parent::set_filename($name, $type);
		}
		return $this;
	}

}
