<?php defined('SYSPATH') OR die('No direct access allowed.');

class custom_valid
{
    public static function username_exists($value, $id=0)
    {
        return !(bool) Database::instance()
			->where(array('username'=>$value, 'id!='=>$id))
			->count_records('users');
    }
    
    public static function email_exists($value, $id=0)
    {
        return !(bool) Database::instance()
			->where(array('email'=>$value, 'id!='=>$id))
			->count_records('users');
    }
}
