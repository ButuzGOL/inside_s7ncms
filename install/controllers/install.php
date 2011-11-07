<?php defined('SYSPATH') OR die('No direct access allowed.');

class Install_Controller extends Template_Controller {

	public function __construct()
	{
		if (file_exists(DOCROOT.'config/database.php'))
			die('S7Ncms is already installed.');
		
		parent::__construct();
		
		$this->template->bind('title', $this->title);
		$this->template->bind('content', $this->content);
		$this->template->bind('error', $this->error);
	}

	public function index()
	{
		Session::instance()->destroy();
		
		url::redirect('install/step_systemcheck');
	}

	public function step_systemcheck()
	{
		$this->title = 'System Check';
		
		$view = new View('step_systemcheck');

		$view->php_version           = version_compare(PHP_VERSION, '5.2', '>=');
		$view->system_directory      = (is_dir(SYSPATH) AND is_file(SYSPATH.'core/Bootstrap'.EXT));
		$view->application_directory = (is_dir(APPPATH) AND is_file(DOCROOT.'application/config/config'.EXT));
		$view->modules_directory     = is_dir(MODPATH);
		$view->config_writable       = (is_dir(DOCROOT.'config') AND is_writable(DOCROOT.'config'));
		$view->cache_writable        = (is_dir(DOCROOT.'config/cache') AND is_writable(DOCROOT.'config/cache'));
		$view->pcre_utf8             = @preg_match('/^.$/u', 'ñ');
		$view->pcre_unicode          = @preg_match('/^\pL$/u', 'ñ');
		$view->reflection_enabled    = class_exists('ReflectionClass');
		$view->filters_enabled       = function_exists('filter_list');
		$view->iconv_loaded          = extension_loaded('iconv');
		$view->mbstring              = ( ! (extension_loaded('mbstring') AND ini_get('mbstring.func_overload') AND MB_OVERLOAD_STRING));
		$view->uri_determination     = isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']);

		if (    $view->php_version
			AND $view->system_directory
			AND $view->application_directory
			AND $view->modules_directory
			AND $view->config_writable
			AND $view->cache_writable
			AND $view->pcre_utf8
			AND $view->pcre_unicode
			AND $view->reflection_enabled
			AND $view->filters_enabled
			AND $view->iconv_loaded
			AND $view->mbstring
			AND $view->uri_determination)
			url::redirect('install/step_database');
		else
		{
			$this->error = 'S7Ncms may not work correctly with your environment.';
		}

		$this->content = $view;
		$this->title = 'System Check';
	}

	public function step_database()
	{
		$this->content = View::factory('step_database')->bind('form', $form);
		$this->title = 'Database Configuration';
		
		$form = array(
			'username' => '',
			'password' => '',
			'hostname' => '',
			'database' => ''
		);

		if ($_POST)
		{
			$data = array(
				'username' => $username = $this->input->post('username'),
				'password' => $password = $this->input->post('password'),
				'hostname' => $hostname = $this->input->post('hostname'),
				'database' => $database = $this->input->post('database'),
				'table_prefix' => '' // TODO
			);

			try
			{
				installer::check_database($username, $password, $hostname, $database);

				Session::instance()->set('database_data', $data);

				url::redirect('install/step_create_data');
			}
			catch (Exception $e)
			{
				$form = arr::overwrite($form, $this->input->post());
				$error = $e->getMessage();

				// TODO create better error messages
				switch ($error)
				{
					case 'access':
						$this->error = 'wrong username or password';
						break;
					case 'unknown_host':
						$this->error = 'could not find the host';
						break;
					case 'connect_to_host':
						$this->error = 'could not connect to host';
						break;
					case 'select':
						$this->error = 'could not select the database';
						break;
					default:
						$this->error = $error;
				}
			}
		}
	}
	
	public function step_create_data()
	{
		$data = Session::instance()->get('database_data');
		$password = text::random('alnum', 8);
		$password_hash = Auth::instance()->hash_password($password);
		
		$sql = View::factory('sql_dump', array('table_prefix' => $data['table_prefix'], 'password_hash' => $password_hash))->render();
		$sql = explode("\n", $sql);
		
		mysql_connect($data["hostname"], $data["username"], $data["password"]);
		mysql_select_db($data["database"]);
		
		$buffer = '';
		foreach ($sql as $line)
		{
			$buffer .= $line;
			if (preg_match('/;$/', $line))
			{
				mysql_query($buffer);
				
				$buffer = '';
			}
			
		}
		
		Session::instance()->set('password', $password);
		
		url::redirect('install/finalize');
	}
	
	public function finalize()
	{
		$data = Session::instance()->get('database_data');
		installer::create_database_config($data['username'], $data['password'], $data['hostname'], $data['database'], $data['table_prefix']);
		
		$password = Session::instance()->get('password');
		Session::instance()->destroy();
		
		$this->content = View::factory('finalize', array('password' => $password));
	}

}
