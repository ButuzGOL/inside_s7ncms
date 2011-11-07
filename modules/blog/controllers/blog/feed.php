<?php defined('SYSPATH') OR die('No direct access allowed.');

class Feed_Controller extends Controller {

	protected $cache;

	public function __construct()
	{
		parent::__construct();

		$this->cache = new Cache(array(
			'lifetime' => 60*60
		));
	}

	public function index()
	{
		header('Content-Type: text/xml; charset=UTF-8', TRUE);

		if ($cache = $this->cache->get('s7nm_blog_feed'))
		{
			echo $cache;
			return;
		}

		$posts = ORM::factory('blog_post')->find_all(10);

		$info = array
		(
			'title' => config::get('s7nm.site_title'),
			'description' => '',
			'link' => url::current_site(),
			'generator' => 'S7Ncms - http://www.s7n.de/'
		);

		$items = array();
		foreach ($posts as $post)
		{
			$items[] = array
			(
				'author'      => 'example@example.com ('.$post->user->username.')',
				'pubDate'     => date('r', $post->date),
				'title'       => $post->title,
				'description' => $post->content,
				'link'        => url::current_site($post->uri),
				'guid'        => url::current_site($post->uri),
			);
		}

		$feed = feed::create($info, $items);
		$this->cache->set('s7nm_blog_feed', $feed);
		echo $feed;
	}

	public function comments()
	{
		header('Content-Type: text/xml; charset=UTF-8', TRUE);

		if ($cache = $this->cache->get('s7nm_blog_feed_comments'))
		{
			echo $cache;
			return;
		}

		$comments = ORM::factory('blog_comment')->orderby('id', 'desc')->find_all(20);

		$info = array
		(
			'title' => config::get('s7nm.site_title').' (Latest Comments)',
			'link' => url::current_site(),
			'generator' => 'S7Ncms - http://www.s7n.de/'
		);

		$items = array();
		foreach ($comments as $comment)
		{
			$items[] = array
			(
				'author'      => html::specialchars($comment->author),
				'pubDate'     => date('r', strtotime($comment->date)),
				'title'       => 'New comment for "'.$comment->blog_post->title.'"',
				'description' => html::specialchars($comment->content),
				'link'        => url::current_site($comment->blog_post->uri),
				'guid'        => url::current_site($comment->blog_post->uri),
			);
		}

		$feed = feed::create($info, $items);
		$this->cache->set('s7nm_blog_feed_comments', $feed);
		echo $feed;
	}

}
