<?php defined('SYSPATH') OR die('No direct access allowed.');

class Blog_comment_Model extends ORM {

	protected $belongs_to = array('blog_post', 'user');

	protected $sorting = array('id' => 'ASC');
	
	public function delete($id = NULL)
	{
		$post = ORM::factory('blog_post', (int) $this->blog_post_id);
		$post->comment_count -= 1;
		$post->save();
		
		parent::delete($id);
	}

}
