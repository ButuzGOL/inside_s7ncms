<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Admin_Controller extends Controller {

	public $session;
	public $db;
	public $head;
	
	// Template view name
	public $template = 'template';
	
	// Default to do auto-rendering
	public $auto_render = TRUE;

	public function __construct()
	{
		parent::__construct();

		$this->session = Session::instance();

		// check if user is logged in or not. also check if he has admin role
		if ( ! Auth::factory()->logged_in('admin'))
		{
			$this->session->set('redirect_me_to', url::current());
			url::redirect('admin/auth/login');
		}
		
		// Load the template
		$this->template = new View($this->template);
		
		$this->db = Database::instance();
		
		$this->head = Head::instance();

		$this->head->title->set('S7Nadmin');

		$this->template->set_global('tasks', array());
		$this->template->set_global('sidebar', array());
        $this->template->set_global('head', $this->head);
        
		$this->template->title = '';
		$this->template->message = $this->session->get('info_message', NULL);
		$this->template->error = $this->session->get('error_message', NULL);
		$this->template->content = '';
		
		$this->template->searchbar = FALSE;
		$this->template->searchvalue = '';
		
		if ($this->auto_render == TRUE)
		{
			// Render the template immediately after the controller method
			Event::add('system.post_controller', array($this, '_render'));
		}
	}

	/**
	 * Render the loaded template.
	 */
	public function _render()
	{
		if ($this->auto_render == TRUE)
		{
			// Render the template when the class is destroyed
			$this->template->render(TRUE);
		}
	}

}
