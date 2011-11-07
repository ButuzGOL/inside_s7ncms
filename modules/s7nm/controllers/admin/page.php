<?php defined('SYSPATH') OR die('No direct access allowed.');

class Page_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->template->tasks = array(
			array('admin/page', __('Show All')),
			array('admin/page/create', __('New Page')),
		);

		$this->head->title->append(__('Pages'));
		$this->template->title = html::anchor('admin/page', __('Pages')).' | ';
	}

	public function index()
	{
		$this->template->content = View::factory('page/index_tree', array(
			'pages' => ORM::factory('page')->find_all()
		));

		$this->head->title->append(__('All Pages'));
		$this->template->title .= __('All Pages');
	}
	
	public function create()
	{
	    $this->head->title->append(__('New Page'));
		$this->template->title .= __('New Page');
		
		$form = Formo::factory()
		    ->add_group('type', array('none' => __('Do nothing'), 'module' => __('Load module'), 'redirect' => __('Redirect to')))
			->add('submit', 'submit', array('label' => __('Save')));
        
        if (count(module::installed_as_array()))
            $form
                ->add_select('module', module::installed_as_array());
        
        if (count(ORM::factory('page')->paths()))
            $form
                ->add_select('redirect', ORM::factory('page')->paths());
        
		foreach (Kohana::config('locale.languages') as $key => $value)
		{
			$form
				->add('text', 'title_'.$key, array('label' => __('Title')))
				->add('text', 'content_'.$key, array('label' => __('Content')))
				->add_rule('title_'.$key, 'required', __('Please choose a title'));
		}
		
		if ($form->validate(TRUE))
		{
			$root = ORM::factory('page')->root(1);
			if ( ! $root->loaded)
			{
				$root->{$root->left_column}  = 1;
				$root->{$root->right_column} = 2;
				$root->{$root->level_column} = 0;
				$root->{$root->scope_column} = 1;
				$root->save();
				$page = $root;
			}
			else
			{
				$page = ORM::factory('page');
				$page->insert_as_last_child($root);
			}


			$title = array();
			foreach (Kohana::config('locale.languages') as $key => $value)
			{
				$page_content = ORM::factory('page_content');
				$page_content->page_id  = $page->id;
				$page_content->language = $key;
				$page_content->title    = $form->{'title_'.$key}->value;
				$page_content->uri      = url::title($form->{'title_'.$key}->value);
				$page_content->content  = $form->{'content_'.$key}->value;
				$page_content->date     = time();
				$page_content->modified = time();
				$page_content->save();
				
				$title[] = $page_content->title;
			}
			
			$page->title = implode(' / ', $title);
			$page->save();
			
			message::info(__('Page created successfully'), 'admin/page');
		}
		
		$this->template->content = View::factory('page/create')->bind('form', $form);
	}
	
	public function edit($id)
	{
	    $this->head->title->append(__('Edit Page'));
		$this->template->title .= __('Edit Page');
	    
		$page = ORM::factory('page', (int) $id);
			
		if ( ! $page->loaded)
			message::error(__('Invalid ID'), 'admin/page');
		
		$form = Formo::factory()
			->add_group('type', array('none' => __('Do nothing'), 'module' => __('Load module'), 'redirect' => __('Redirect to')))
			->add('submit', 'submit', array('label' => __('Save')));
        
        if (count(module::installed_as_array()))
            $form
                ->add_select('module', module::installed_as_array(), array('value' => $page->target));
        
        if (count($page->paths()))
            $form
                ->add_select('redirect', $page->paths(), array('value' => $page->target));
        
		foreach (Kohana::config('locale.languages') as $key => $value)
		{
			$page_content = ORM::factory('page_content')->where(array('page_id' => $page->id, 'language' => $key))->find();
			$form
				->add('text', 'title_'.$key, array('label' => __('Title'), 'value' => $page_content->title))
				->add('text', 'content_'.$key, array('label' => __('Content'), 'value' => $page_content->content))
				->add_rule('title_'.$key, 'required', __('Please choose a title'));
		}
		
		if ($form->validate())
		{
			$title = array();
			foreach (Kohana::config('locale.languages') as $key => $value)
			{
				$page_content = ORM::factory('page_content')->where(array('page_id' => $page->id, 'language' => $key))->find();
				if ( ! $page_content->loaded)
				{
					$page_content->page_id  = $page->id;
					$page_content->language = $key;
					$page_content->date = date("Y-m-d H:i:s");
				}
				$page_content->title    = $form->{'title_'.$key}->value;
				$page_content->uri      = url::title($form->{'title_'.$key}->value);
				$page_content->content  = $form->{'content_'.$key}->value;
				$page_content->modified = date("Y-m-d H:i:s");
				$page_content->save();
				
				$title[] = $page_content->title;
			}
			
			$type = NULL;
			$target = NULL;

			$_type = NULL;
			foreach ($form->type->elements as $key => $value)
			{
				if ($form->type->$key->checked)
				{
					$_type = $value;
					break;
				}
			}

			if ($_type == 'redirect')
			{
				$redirect = trim($form->redirect->value);
				if ( ! empty($redirect))
				{
					$type = 'redirect';
					$target = $redirect;
				}
			}
			elseif ($_type == 'module')
			{
				$module = trim($form->module->value);
				if ( ! empty($module))
				{
					$type = 'module';
					$target = $module;
				}
			}

			$page->type = $type;
			$page->target = $target;
			$page->title = implode(' / ', $title);
			$page->save();
			
			message::info(__('Page edited successfully'), 'admin/page');
		}
		
		$this->template->content = View::factory('page/edit')->bind('form', $form)
		                                                     ->bind('page', $page);

	}
	
	public function delete($id)
	{
		$page = ORM::factory('page', (int) $id);
		
		if ( ! $page->loaded)
			message::error(__('Invalid ID'), 'admin/page');

		$page->delete();

		message::info(__('Page deleted successfully'), 'admin/page');
	}
	
	public function save_tree()
	{
		$tree = json_decode($this->input->post('tree', NULL), TRUE);

		$this->tree = array();
		$this->counter = 0;
		$this->level_zero = 0;

		$this->__calculate_mptt($tree);

		if ($this->level_zero > 1)
		{
			$this->session->set_flash('error_message', __('Page order could not be saved.'));
			exit;
		}

		foreach($this->tree as $node)
		{
			$this->db
				->set(array('lvl' => $node['level'], 'lft' => $node['lft'], 'rgt' => $node['rgt']))
				->where('id', $node['id'])
				->update('pages');
		}

		$this->session->set_flash('info_message', __('Page order saved successfully'));
		exit;
	}
	
	private function __calculate_mptt($tree, $parent = 0, $level = 0)
	{
		foreach ($tree as $key => $children)
		{
			$id = substr($key, 5);

			$left = ++$this->counter;

			if ( ! empty($children))
				$this->__calculate_mptt($children, $id, $level+1);

			$right = ++$this->counter;

			if ($level === 0)
				$this->level_zero++;

			$this->tree[] = array(
				'id' => $id,
				'level' => $level,
				'lft' => $left,
				'rgt' => $right
			);
		}
	}
}
