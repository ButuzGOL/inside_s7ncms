<?php defined('SYSPATH') OR die('No direct access allowed.');

class installer {

	public static function check_system()
	{
		
	}

	public static function check_database($username, $password, $hostname, $database)
	{
		if ( ! $link = @mysql_connect($hostname, $username, $password)) {
			if (strpos(mysql_error(), 'Access denied') !== FALSE)
				throw new Exception('access');
				
			elseif (strpos(mysql_error(), 'server host') !== FALSE)
				throw new Exception('unknown_host');
				
			elseif (strpos(mysql_error(), 'connect to') !== FALSE)
				throw new Exception('connect_to_host');
				
			else
				throw new Exception(mysql_error());
		}

		if ( ! $select = mysql_select_db($database, $link)) {
			throw new Exception('select');
		}

		return TRUE;
	}
	
	public function create_database_config($username, $password, $hostname, $database, $table_prefix)
	{
		$config = new View('database_config');
		$config->username     = $username;
		$config->password     = $password;
		$config->hostname     = $hostname;
		$config->database     = $database;
		$config->table_prefix = $table_prefix;

		file_put_contents(DOCROOT.'config/database.php', $config);
	}

}
