<?php defined('SYSPATH') OR die('No direct access allowed.');

class Modules_Controller extends Admin_Controller {

	public  function __construct()
	{
		parent::__construct();

		$this->head->title->append(__('Modules'));
		$this->template->title = __('Modules');
	}
	
	public function index()
	{
		$this->template->content = View::factory('modules/index', array(
			'modules' => module::available()
		));
    }
    
    public function install($module)
    {
		require_once(MODPATH.$module.'/helpers/'.$module.'_installer.php');

		call_user_func($module.'_installer::install');

		message::info(__('Module installed successfully'), 'admin/modules');
    }

    public function uninstall($module)
    {
    	require_once(MODPATH.$module.'/helpers/'.$module.'_installer.php');

    	call_user_func($module.'_installer::uninstall');

    	message::info(__('Module uninstalled successfully'), 'admin/modules');
    }
    
    public function status($module, $new_status)
    {
    	module::change_status($module, $new_status);

    	message::info(__('Module status successfully changed'), 'admin/modules');
    }
}
