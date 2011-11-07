<?php defined('SYSPATH') OR die('No direct access allowed.');

class Module_Model extends ORM {
    
    public function unique_key($id = NULL)
	{
		if( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
			return 'name';

		return parent::unique_key($id);
	}
}
