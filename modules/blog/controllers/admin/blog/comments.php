<?php defined('SYSPATH') OR die('No direct access allowed.');

class Comments_Controller extends Admin_Controller {

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
		$this->template->content = new View('blog/comments');
		$this->template->content->comments = ORM::factory('blog_comment')->orderby('id', 'DESC')->find_all();

		$this->head->title->append(__('All comments'));
		$this->template->title .= __('All comments');
	}

	public function view($blog_post_id)
	{
		$post = ORM::factory('blog_post', (int) $blog_post_id);
		
		if ( ! $post->loaded)
			message::error(__('Invalid ID'), 'admin/blog');

		$this->template->content = new View('blog/comments');
		$this->template->content->comments = $post->blog_comments;

		$this->head->title->append(__('Comments for: %title', array('%title' => $post->title)));
		$this->template->title .= __('Comments for: %title', array('%title' => $post->title));
	}

	public function edit($id)
	{
		$comment = ORM::factory('blog_comment', (int) $id);
		
		if ( ! $comment->loaded)
			message::error(__('Invalid ID'), 'admin/blog');
		
		$this->head->title->append(__('Edit comment #%id', array('%id' => $comment->id)));
		$this->template->title .= __('Edit comment #%id', array('%id' => $comment->id));
		
		$form = Formo::factory()
			->add('text', 'author', array('label' => __('Author'), 'value' => $comment->author))
			->add('text', 'email', array('label' => __('Email'), 'value' => $comment->email))
			->add('text', 'url', array('label' => __('Homepage'), 'value' => $comment->url))
			->add('textarea', 'content', array('label' => __('Comment'), 'value' => $comment->content))
			->add('submit', 'submit', array('label' => __('Save')));
			
		if($form->validate())
		{
			$comment->author = $form->author->value;
			$comment->email = $form->email->value;
			$comment->url = $form->url->value;
			$comment->content = $form->content->value;
			$comment->save();

			Cache::instance()->delete('s7nm_blog_feed_comments');

			message::info(__('Comment edited successfully'), 'admin/blog/comments/view/'.$comment->blog_post_id);
		}
		
		$this->template->content = View::factory('blog/editcomment')->bind('form', $form);
	}
	
	public function delete($id)
	{
		$comment = ORM::factory('blog_comment', (int) $id);
		
		if ( ! $comment->loaded)
			message::error(__('Invalid ID'), 'admin/blog');
		
		$blog_post_id = $comment->blog_post_id;
		$comment->delete();

		Cache::instance()->delete('s7nm_blog_feed_comments');

		message::info(__('Comment deleted successfully'), 'admin/blog/comments/view/'.$blog_post_id);
	}
	
	public function close($blog_post_id)
	{
		$this->comments_status('open', $blog_post_id);
	}
	
	public function open($blog_post_id)
	{
		$this->comments_status('close', $blog_post_id);
	}
}
