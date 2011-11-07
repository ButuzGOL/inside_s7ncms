<?php defined('SYSPATH') OR die('No direct access allowed.');

class Auth_Controller extends Controller {

	public function index()
	{
		url::redirect('admin/auth/login');
	}

	public function login()
	{
		if (Auth::instance()->logged_in())
		{
			if (Auth::instance()->logged_in('admin'))
				url::redirect('admin');
			else
				url::redirect();
		}
		$form = Formo::factory()
			->add('text', 'username', array('label' => __('Username')))
			->add('password', 'password', array('label' => __('Password')))
			->add('submit', 'submit', array('label' => __('Login')))
			
			->add_rule('username', 'required', __('You must enter a username'))
			->add_rule('password', 'required', __('You must enter a password'));
			
		if ($form->validate(TRUE))
		{
			// Load the user
			$user = ORM::factory('user', $form->username->value);

			// Attempt a login
			if ($user->loaded AND Auth::instance()->login($user, $form->password->value))
			{
				$url = Session::instance()->get_once('redirect_me_to');
				url::redirect(empty($url) ? 'admin' : $url);
			}
			
			$error = __('Invalid username or password');
		}

        $view = View::factory('auth/login')
                    ->bind('form', $form)
                    ->bind('error', $error);
		$view->render(TRUE);
	}
	
	public function logout()
	{
		// Load auth and log out
		Auth::instance()->logout(TRUE);

		// Redirect back to the login page
		url::redirect('admin/auth/login');
	}
}
