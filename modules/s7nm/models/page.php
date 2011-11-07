<?php defined('SYSPATH') OR die('No direct access allowed.');

class Page_Model extends ORM_MPTT {
    
    protected $sorting = array('lft' => 'ASC');
    public static $page_cache = array();
    private $_identifier;
    
    private $page_columns = array('uri', 'title');
    
    public function __construct($id = NULL)
	{
		parent::__construct($id);

		$this->_identifier = text::random();
	}
	
	public function __get($column)
	{
		if (in_array($column, $this->page_columns))
		{
			if ( ! isset(self::$page_cache[$this->_identifier]))
				self::$page_cache[$this->_identifier] = ORM::factory('page_content')
					->where(array('page_id' => $this->id, 'language' => Router::$language))
					->find();

			return self::$page_cache[$this->_identifier]->$column;
		}

		return parent::__get($column);
	}
    
    public function paths()
    {
		$pages = $this->find_all();

		$paths = array();
		foreach ($pages as $page)
		{
			if ($page->id === $this->id)
				continue;

			$titles = array();

			$path = $page->parents(FALSE)->find_all();

			foreach ($path as $pagex)
				$titles[] = $pagex->title;

			$titles[] = $page->title;
			$paths[$page->id] = implode(' &rarr; ', $titles);
		}

		return $paths;
    }
    
    public function delete()
	{
		$descendants = $this->descendants(TRUE)->find_all();
		foreach ($descendants as $descendant)
			$this->db->delete('page_contents', array('page_id' => $descendant->id));
		
		return parent::delete($this->id);
	}
	
}
