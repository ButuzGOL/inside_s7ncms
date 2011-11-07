<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['default'] = array(); // prevents merging the database config with system/config/database.php

if (file_exists(DOCROOT.'config/database.php'))
	require_once(DOCROOT.'config/database.php');
else
{
	header('Location: install.php');
	exit;
}
