<?php defined('SYSPATH') OR die('No direct access allowed.');

class Blog_Controller extends Index_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->head->link->append(url::current_site('feed'));

		if (config::get('blog.enable_tagcloud') === 'yes')
		{
			$tags = ORM::factory('blog_post')->tags();
			if ( ! empty($tags))
			{
				Sidebar::instance()->add(
					'Tagcloud',
					array('tags' => $tags)
				);
			}
		}
	}
	
	public function __call($method, $arguments)
	{
		$this->index($method);
	}
	
	public function page()
	{
		$this->index();
	}

	public function index($uri = NULL)
	{
		if ($uri !== NULL)
		{
			$this->article($uri);
			return;
		}
		
		$this->template->content = View::factory('blog/index')
			->bind('pagination', $pagination)
			->bind('posts', $posts);

		$pagination = new Pagination(array(
			'uri_segment'    => 'page',
			'items_per_page' => (int) config::get('blog.items_per_page'),
			'total_items'    => ORM::factory('blog_post')->count_posts(),
			'auto_hide'      => TRUE,
			'style'          => 'digg'
		));
		
		$posts = ORM::factory('blog_post')->find_all($pagination->items_per_page, $pagination->sql_offset);
	}
	
	public function article($uri)
	{
		$this->template->content = View::factory('blog/view')
			->bind('post', $post)
			->bind('comments', $comments)
			->bind('form', $form1);
			
		$post = ORM::factory('blog_post', (string) $uri);

		// Show 404 if we don't find posts
		if ( ! $post->loaded)
			Event::run('system.404');

		$comments = $post->blog_comments;
		
		$this->head->title->prepend($post->title);

		if ( ! ($post->comment_status === 'open' AND config::get('blog.comment_status') === 'open'))
			return;
			
		$form = Formo::factory()
			->add('text', 'author', array('label' => __('Name')))
			->add('text', 'email', array('label' => __('Email')))
			->add('text', 'url', array('label' => __('Homepage')))
			->add('textarea', 'content', array('label' => __('Comment')))
			->add('submit', 'submit', array('label' => __('Submit')))
			
			->pre_filter('all', 'trim')
			->pre_filter('author', 'security::xss_clean')
			->pre_filter('content', 'security::xss_clean')
			->pre_filter('url', 'security::xss_clean')
			->pre_filter('url', 'format::url')
			
			->add_rule('author', 'required', __('You must provide your name'))
			->add_rule('author', 'length[2,40]', __('Your Name is too long'))
			->add_rule('email', 'valid::email', __('Email address is not valid'))
			->add_rule('content', 'required', __('You must enter a comment'));

		if (config::get('blog.enable_captcha') === 'yes')
		{
			$form->add('captcha', 'security', array('label' => __('Security code')));
			$form->security->error_msg = __('Invalid security code');
		}

		if ($form->validate())
		{
			$comment = ORM::factory('blog_comment');
			$comment->author  = $form->author->value;
			$comment->email   = $form->email->value;
			$comment->content = $form->content->value;
			$comment->url     = $form->url->value;
			$comment->ip      = $this->input->ip_address();
			$comment->date    = time();

			$post->add_comment($comment);
			
			Event::run('blog.comment_added', $comment);

			Cache::instance()->delete('s7nm_blog_feed');
			Cache::instance()->delete('s7nm_blog_feed_comments');

			url::redirect(url::current_site($post->uri));
		}

		$form1 = View::factory('blog/form_comment')->bind('form', $form);
	}

	public function tag($tag)
	{
		$this->template->content = View::factory('blog/index')
			->bind('posts', $posts);
		
		$posts = ORM::factory('blog_post')->like('tags', $tag)->find_all();
	}

}
