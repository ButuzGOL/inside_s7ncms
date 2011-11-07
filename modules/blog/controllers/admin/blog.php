<?php defined('SYSPATH') OR die('No direct access allowed.');

class Blog_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->template->tasks = array(
			array('admin/blog/create', __('New Post')),
			array('admin/blog/comments', __('All comments')),
			array('admin/blog/settings', __('Edit Settings'))
		);

		$this->head->title->append('Blog');
		$this->template->title = html::anchor('admin/blog', 'Blog').' | ';
	}

	public function index()
	{
		$this->template->searchbar = TRUE;

		$q = trim($this->input->get('q'));

		if ( ! empty($q))
		{
			$this->template->searchvalue = $q;
			$this->template->title .= __('Filter: %filter', array('%filter' => $q));
			$this->head->title->append(__('Filter: %filter', array('%filter' => $q)));

			$posts = ORM::factory('blog_post')->orlike(array(
				'title' => $q,
				'content' => $q,
				'tags' => $q
			))->find_all();
		}
		else
		{
			$this->template->title .= __('All Posts');
			$this->head->title->append(__('All Posts'));

			$posts = ORM::factory('blog_post')->find_all();
		}

		$this->template->content = View::factory('blog/index', array('posts' => $posts));
	}

	public function create()
	{
		$this->head->title->append(__('New Post'));
		$this->template->title .= __('New Post');
		
		$form = Formo::factory()
			->add('text', 'title', array('label' => __('Title')))
			->add('textarea', 'content', array('label' => __('Content')))
			->add('text', 'tags', array('label' => __('Tags: <small>(Comma separated)</small>')))
			->add('submit', 'submit', array('label' => __('Submit')))
			
			->add_rule('title', 'required', __('Please choose a title'));
			
		if($form->validate())
		{
			$post = ORM::factory('blog_post');
			$post->user_id = Auth::instance()->get_user()->id;
			$post->title = $form->title->value;
			$post->uri = blog::unique_title($form->title->value);
			$post->content = $form->content->value;
			$post->tags = $form->tags->value;
			$post->save();

			Cache::instance()->delete('s7nm_blog_feed');
			Cache::instance()->delete_tag('route');

			message::info(__('Post created successfully'), 'admin/blog');
		}
		
		$this->template->content = View::factory('blog/create')->bind('form', $form);
	}

	public function edit($id)
	{
		$post = ORM::factory('blog_post', (int) $id);
		
		if ( ! $post->loaded)
			message::error(__('Invalid ID'), 'admin/blog');
		
		$this->head->title->append(__('Edit: %title', array('%title' =>$post->title)));
		$this->template->title .= __('Edit: %title', array('%title' =>$post->title));
			
		$form = Formo::factory()
			->add('text', 'title', array('label' => __('Title'), 'value' => $post->title))
			->add('textarea', 'content', array('label' => __('Content'), 'value' => $post->content))
			->add('text', 'tags', array('label' => __('Tags: <small>(Comma separated)</small>'), 'value' => $post->tags))
			->add('submit', 'submit', array('label' => __('Submit')))
			
			->add_rule('title', 'required', __('Please choose a title'));
		
		if($form->validate())
		{
			if ($form->title->value !== $post->title)
				$post->uri = blog::unique_title($form->title->value);
			
			$post->title = $form->title->value;
			$post->content = $form->content->value;
			$post->tags = $form->tags->value;
			$post->save();

			Cache::instance()->delete('s7nm_blog_feed');
			Cache::instance()->delete_tag('route');

			message::info(__('Post edited successfully'), 'admin/blog/edit/'. (int) $id);
		}

		$this->template->content = View::factory('blog/edit')->bind('form', $form);
	}

	public function delete($id)
	{
		$post = ORM::factory('blog_post', (int) $id);

		if ( ! $post->loaded)
			message::error(__('Invalid ID'), 'admin/blog');
		
		$post->delete();

		Cache::instance()->delete('s7nm_blog_feed');
		Cache::instance()->delete('s7nm_blog_feed_comments');
		Cache::instance()->delete_tag('route');

		message::info(__('Post deleted successfully'), 'admin/blog');
	}

	public function settings()
	{
		$this->head->title->append(__('Settings'));
		$this->template->title .= __('Settings');
			
		$form = Formo::factory()
			->add('text', 'items_per_page', array('label' => __('Blog entries per page'), 'value' => config::get('blog.items_per_page')))
			->add('checkbox', 'enable_captcha', array('label' => __('Enable captcha'), 'checked' => (config::get('blog.enable_captcha') === 'yes')))
			->add('checkbox', 'enable_tagcloud', array('label' => __('Enable tag cloud'), 'checked' => (config::get('blog.enable_tagcloud') === 'yes')))
			->add('checkbox', 'comment_status', array('label' => __('Enable comments'), 'checked' => (config::get('blog.comment_status') === 'open')))
			->add('submit', 'submit', array('label' => __('Save')))
			
			->add_rule('items_per_page', 'required', __('Please enter a number'))
			->add_rule('items_per_page', 'digit', __('This must be a number'));

		
		if($form->validate())
		{
			config::set('blog.enable_captcha', $form->enable_captcha->checked ? 'yes' : 'no');
			config::set('blog.enable_tagcloud', $form->enable_tagcloud->checked ? 'yes' : 'no');
			config::set('blog.comment_status', $form->comment_status->checked ? 'open' : 'closed');
			config::set('blog.items_per_page', $form->items_per_page->value);

			message::info(__('Settings changed successfully'), 'admin/blog/settings');
		}
		
		$this->template->content = View::factory('blog/settings')->bind('form', $form);
	}

}
