<?php defined('SYSPATH') OR die('No direct access allowed.');

class blog {

	public static function unique_title($title)
	{
		$uri = url::title($title);
		
		$result = Database::instance()->select('uri')->like('uri', $uri.'%', FALSE)->get('blog_posts');
		
		if (count($result) > 0)
		{
			$max = 0;
			foreach ($result as $row)
			{
				$suffix = substr($row->uri, strlen($uri)+1);
				if(ctype_digit($suffix) AND $suffix > $max)
					$max = $suffix;
			}

			if ($max === 0)
				$uri .= '-2';
			else
				$uri .= '-'.($max+1);
		}
		
		return $uri;
	}

}
