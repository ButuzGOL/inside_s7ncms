<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Default language locale name(s).
 * First item must be a valid i18n directory name, subsequent items are alternative locales
 * for OS's that don't support the first (e.g. Windows). The first valid locale in the array will be used.
 * @see http://php.net/setlocale
 */
$config['language'] = array('ru_RU', 'Russian_Russia');

/**
 * Available languages.
 */
$config['languages'] = array
(
	'ru' => array('language' => array('ru_RU', 'Russian_Russia'), 'name' => 'Russian'),
	'en' => array('language' => array('en_US', 'English_United States'), 'name' => 'English')
);

/**
 * Locale timezone. Defaults to use the server timezone.
 * @see http://php.net/timezones
 */
$config['timezone'] = 'Europe/Russia';
