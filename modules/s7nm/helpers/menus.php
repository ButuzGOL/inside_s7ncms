<?php defined('SYSPATH') OR die('No direct access allowed.');

class menus_Core {

	public static function modules()
	{
		$query = Database::instance()->where('status', 'on')->get('modules');

		if (count($query) === 0)
			return '';

		$menu = '';
		foreach ($query->result() as $result)
		{
			if( ! is_file(MODPATH.$result->name.'/module.xml'))
				continue;

			$xml = simplexml_load_file(MODPATH.$result->name.'/module.xml');
			$title = (string) $xml->admin_menu_title;

			if(empty($title))
				continue;

			$menu .= '<li>'.html::anchor('admin/'. (string) $xml->uri, $title).'</li>';

		}

		return $menu;
	}

}
