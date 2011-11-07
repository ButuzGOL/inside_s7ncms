<?php defined('SYSPATH') OR die('No direct access allowed.');

class Index_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->session = Session::instance();
		$this->head = Head::instance();
		
		$this->head->title->set(config::get('s7nm.site_title'));
		$this->template->head = $this->head;
	}

}
