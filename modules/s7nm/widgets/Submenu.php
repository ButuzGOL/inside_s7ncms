<?php defined('SYSPATH') OR die('No direct access allowed.');

class Submenu_Widget extends Widget {

	public function render()
	{
		$menu = Menu::instance('submenu')->render();

		if (empty($menu))
			return '';

		return View::factory('widgets/submenu')->set(array(
			'menu' => $menu
		))->render();
	}

}
