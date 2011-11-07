<?php defined('SYSPATH') OR die('No direct access allowed.');

class Page_Controller extends Index_Controller {

	public function index($id)
	{
		$page = ORM::factory('page', $id);

		if( ! $page->loaded)
    		Event::run('system.404');

    	$page_content = ORM::factory('page_content')->where(array('page_id' => $page->id, 'language' => Router::$language))->find();

		$this->template->content = View::factory('page/default', array('page' => $page_content));

		$this->head->title->append($page->title);
	}

}
