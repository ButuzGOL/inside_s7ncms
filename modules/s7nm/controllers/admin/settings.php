<?php defined('SYSPATH') OR die('No direct access allowed.');

class Settings_Controller extends Admin_Controller {

	public function index()
	{
		$this->head->title->append(__('Settings'));
		$this->template->title = __('Settings');
		
		$form = Formo::factory()
			->add('text', 'site_title', array('label' => __('Site title'), 'value' => config::get('s7nm.site_title')))
			->add_select('theme', theme::available(), array('label' => __('Theme'), 'value' => config::get('s7nm.theme')))
			->add_select('admintheme', theme::available(ADMINTHEMEPATH), array('label' => __('Admin theme'), 'value' => config::get('s7nm.admintheme')))
			->add('submit', 'submit', array('label' => __('Save')));
		
		if ($form->validate(TRUE))
		{
			config::set('s7nm.site_title', $form->site_title->value);
			config::set('s7nm.theme', $form->theme->value);
			config::set('s7nm.admintheme', $form->theme->value);
			
			message::info(__('Settings edited successfully'), 'admin/settings');
		}

		$this->template->content = View::factory('settings/settings')->bind('form', $form);
	}

}
