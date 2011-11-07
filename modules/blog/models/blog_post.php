<?php defined('SYSPATH') OR die('No direct access allowed.');

class Blog_post_Model extends ORM {

	protected $has_many = array('blog_comments');
	protected $belongs_to = array('user');

	protected $sorting = array('id' => 'DESC');

	protected $load_with = array('user');

	/**
	 * Allows Blogposts to be loaded by id or uri title.
	 */
	public function unique_key($id = NULL)
	{
		if( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
			return 'uri';

		return parent::unique_key($id);
	}

	public function save()
	{
		if ( ! $this->loaded)
			$this->date = time();

		$this->modified = time();

		parent::save();
	}
	
	public function delete($id = NULL)
	{
		Database::instance()->where('blog_post_id', (int) $this->id)->delete('blog_comments');
		
		parent::delete($id);
	}

	public function count_posts()
	{
		return count(Database::instance()->select('id')->get('blog_posts'));
	}

	public function tags()
	{
		$tags = array();
		$query = Database::instance()->select('tags')->get('blog_posts');

		foreach ($query as $result)
		{
            $exploded = explode(',', $result->tags);

            foreach($exploded as $tag) {
                $tag = trim($tag);

                if(empty($tag))
                	continue;

                if(array_key_exists($tag,$tags))
	                $tags[$tag]++;
	            else
	                $tags[$tag] = 1;
            }
        }

        $result = array();
        foreach ($tags as $title => $count)
        	$result[] = array('title' => $title, 'count' => $count, 'link' => url::current_site('tag/'.$title));

        return $result;
	}
	
	/**
	 * increment the comment counter on each new comment
	 */
	public function add_comment(ORM $object)
	{
		/*
		 * set the user_id if the poster is logged in
		 */
		if(Auth::instance()->logged_in())
			$object->user_id = Auth::instance()->get_user()->id;

		$object->blog_post_id = $this->id;
		$object->save();

		$this->comment_count += 1;
		$this->save();
	}

}
