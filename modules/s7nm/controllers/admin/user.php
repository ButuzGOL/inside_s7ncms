<?php defined('SYSPATH') OR die('No direct access allowed.');

class User_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->template->tasks = array(
			array('admin/user/create', __('New User'))
		);

		$this->head->title->append(__('Users'));
		$this->template->title = html::anchor('admin/user', __('Users')).' | ';
	}

	public function index()
	{
		$this->template->content = View::factory('user/index', array('users' => ORM::factory('user')->find_all()));

		$this->head->title->append(__('All Users'));
		$this->template->title .= __('All Users');
	}
	
	public function create()
	{
		$this->head->title->append(__('New User'));
		$this->template->title .= __('New User');

		$form = Formo::factory()
			->add('text', 'username', array('label' => __('Username')))
			->add('text', 'email', array('label' => __('Email')))
			->add('password', 'password', array('label' => __('Password')))
			->add('password', 'password_confirm', array('label' => __('Confirm password')))
			->add('submit', 'submit', array('label' => __('Save')))

			->add_rule('username', 'required', __('You must enter a username'))
			->add_rule('username', 'custom_valid::username_exists', __('Username exists'))
			->add_rule('email', 'required', __('You must enter an email'))
			->add_rule('email', 'valid::email', __('Email address is not valid'))
			->add_rule('email', 'custom_valid::email_exists', __('Email exists'))
			->add_rule('password', 'required', __('You must enter a password'))
			->add_rule('password', 'matches[password_confirm]', __('The passwords doesn\'t match'))
			->add_rule('password_confirm', 'required', __('You must confirm the password'));

		if ($form->validate())
		{
			$user = ORM::factory('user');
			$user->username = $form->username->value;
			$user->email = $form->email->value;
			$user->password = $form->password->value;
			$user->registered_on = time();
			$user->add(ORM::factory('role', 'login'));
			$user->add(ORM::factory('role', 'admin'));
			$user->save();

			message::info(__('User created successfully'), 'admin/user');
		}

		$this->template->content = View::factory('user/create')->bind('form', $form);
	}
	
	public function edit($id)
	{
		$this->head->title->append(__('Edit User'));
		$this->template->title .= __('Edit User');
		
		$user = ORM::factory('user', (int) $id);

		if ( ! $user->loaded)
			message::error(__('Invalid ID'), 'admin/user');
			
		$form = Formo::factory()
			->add('text', 'username', array('label' => __('Username'), 'value' => $user->username))
			->add('text', 'email', array('label' => __('Email'), 'value' => $user->email))
			->add('password', 'password', array('label' => __('Password'), 'required' => FALSE))
			->add('password', 'password_confirm', array('label' => __('Confirm password'), 'required' => FALSE))
			->add('submit', 'submit', array('label' => __('Save')))
            
            ->add_rule('username', 'required', __('You must enter a username'))
			->add_rule('username', 'custom_valid::username_exists['.(int) $id.']', __('Username exists'))
			->add_rule('email', 'required', __('You must enter an email'))
			->add_rule('email', 'valid::email', __('Email address is not valid'))
			->add_rule('email', 'custom_valid::email_exists['.(int) $id.']', __('Email exists'))
			->add_rule('password', 'matches[password_confirm]', __('The passwords doesn\'t match'));

		if ($form->validate())
		{
		    $user->username = $form->username->value;
			$user->email = $form->email->value;
			if ( ! empty($form->password->value))
				$user->password = $form->password->value;
			$user->save();

			message::info(__('User edited successfully'), 'admin/user');
		}

		$this->template->content = View::factory('user/edit')->bind('form', $form);
	}

    public function delete($id)
	{
		$user = ORM::factory('user', (int) $id);

		if ( ! $user->loaded)
			message::error(__('Invalid ID'), 'admin/user');

		if ($user->id === Auth::instance()->get_user()->id)
			message::error(__('You can\'t delete yourself'), 'admin/user');

		$user->remove(ORM::factory('role', 'login'));
		$user->remove(ORM::factory('role', 'admin'));
		$user->delete();

		message::info(__('User deleted successfully'), 'admin/user');
	}
}
