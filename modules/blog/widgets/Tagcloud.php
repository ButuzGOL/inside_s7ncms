<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tagcloud_Widget extends Widget {

	public function render()
	{
		return View::factory('widgets/tagcloud')->set(array(
			'tags' => new Tagcloud($this->config['tags'], config::get('blog.tags_minsize'), config::get('blog.tags_maxsize'))
		))->render();
	}

}
