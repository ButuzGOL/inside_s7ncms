<?php defined('SYSPATH') OR die('No direct access allowed.');

class language_Core {
	
	public static $available_languages = array();
	public static $browser_language = NULL;
	
	public static function setup()
	{
		language::$available_languages = Kohana::config('locale.languages');

		if (Router::$language === NULL)
		{
			$redirect = NULL;
			if (empty(Router::$current_uri))
			{
				if (($lang = language::browser_language()) !== '')
				{
					$redirect = $lang;
				}
				else
				{
					reset(language::$available_languages);
					$redirect = key(language::$available_languages);
				}
				
			}
			else
			{
				if (($lang = language::browser_language()) !== '')
				{
					$redirect = $lang.'/'.Router::$current_uri;
				}
				else
				{
					reset(language::$available_languages);
					$redirect = key(language::$available_languages).'/'.Router::$current_uri;
				}
			}
			url::redirect($redirect);
		}
		
		Kohana::config_set('locale.language', language::$available_languages[Router::$language]['language']);
		I18n::$lang = language::$available_languages[Router::$language]['language'][0];
	}
	
	public static function browser_language()
	{
		if (language::$browser_language === NULL)
		{
			language::$browser_language = '';
			$browser_languages = Kohana::user_agent('languages');

			foreach($browser_languages as $language)
			{
				if (strlen($language) == 2 AND array_key_exists($language, language::$available_languages))
				{
					language::$browser_language = strtolower($language);
					break;
				}
			}
		}

		return language::$browser_language;
	}
}
