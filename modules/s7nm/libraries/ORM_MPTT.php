<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Modified Preorder Tree Traversal Class.
 *
 * @package MPTT
 * @author Mathew Davies
 * @author Kiall Mac Innes
 */
abstract class ORM_MPTT_Core extends ORM
{
	/**
	 * @access public
	 * @var string left column name.
	 */
	public $left_column = 'lft';
	
	/**
	 * @access public
	 * @var string right column name.
	 */
	public $right_column = 'rgt';
	
	/**
	 * @access public
	 * @var string level column name.
	 */
	public $level_column = 'lvl';
	
	/**
	 * @access public
	 * @var string scope column name.
	 **/
	public $scope_column = 'scope';
	
	/**
	 * @access protected
	 * @var string mptt view folder.
	 */
	protected $directory = 'mptt';
	
	/**
	 * @access protected
	 * @var string default view folder.
	 */
	protected $style = 'default';

	/**
	 * Constructor
	 *
	 * @access public
	 * @param integer $id
	 */
	public function __construct($id = NULL)
	{
		// Prepare the directory var...
		$this->directory = ($this->directory === '') ? '' : trim($this->directory, '/').'/';
		
		parent::__construct($id);
	}

	/**
	 * New scope
	 * This also double as a new_root method allowing
	 * us to store multiple trees in the same table.
	 *
	 * @param integer $scope New scope to create.
	 * @return boolean
	 **/
	public function new_scope($scope, array $additional_fields = array())
	{
		// Make sure the specified scope doesn't already exist.
		$search = ORM_MPTT::factory($this->object_name)->where($this->scope_column, $scope)->find_all();

		if ($search->count() > 0 )
			return FALSE;
		
		// Create a new root node in the new scope.
		$this->{$this->left_column} = 1;
		$this->{$this->right_column} = 2;
		$this->{$this->level_column} = 0;
		$this->{$this->scope_column} = $scope;
		
		// Other fields may be required.
		if ( ! empty($additional_fields))
		{
			foreach ($additional_fields as $column => $value)
			{
				$this->{$column} = $value;
			}
		}
		
		parent::save();
		
		return $this;
	}

	/**
	 * Locks table.
	 *
	 * @access private
	 */
	private function lock()
	{
		$this->db->query('LOCK TABLE '.$this->table_name.' WRITE');
	}
	
	/**
	 * Unlock table.
	 *
	 * @access private
	 */
	private function unlock()
	{
		$this->db->query('UNLOCK TABLES');
	}

	/**
	 * Does the current node have children?
	 *
	 * @access public
	 * @return bool
	 */
	public function has_children()
	{
		return (($this->{$this->right_column} - $this->{$this->left_column}) > 1);
	}
	
	/**
	 * Is the current node a leaf node?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_leaf()
	{
		return ! $this->has_children();
	}
	
	/**
	 * Is the current node a descendant of the supplied node.
	 *
	 * @access public
	 * @param ORM_MPTT $target Target
	 * @return bool
	 */
	public function is_descendant($target)
	{
		return ($this->{$this->left_column} > $target->{$this->left_column} AND $this->{$this->right_column} < $target->{$this->right_column} AND $this->{$this->scope_column} = $target->{$this->scope_column});
	}
	
	/**
	 * Is the current node a direct child of the supplied node?
	 *
	 * @access public
	 * @param ORM_MPTT $target Target
	 * @return bool
	 */
	public function is_child($target)
	{
		return ($this->parent->{$this->primary_key} === $target->{$this->primary_key});
	}
	
	/**
	 * Is the current node the direct parent of the supplied node?
	 *
	 * @access public
	 * @param ORM_MPTT $target Target
	 * @return bool
	 */
	public function is_parent($target)
	{
		return ($this->{$this->primary_key} === $target->parent->{$this->primary_key});
	}
	
	/**
	 * Is the current node a sibling of the supplied node
	 *
	 * @access public
	 * @param ORM_MPTT $target Target
	 * @return bool
	 */
	public function is_sibling($target)
	{
		if ($this->{$this->primary_key} === $target->{$this->primary_key})
			return FALSE;
		
		return ($this->parent->{$this->primary_key} === $target->parent->{$this->primary_key});
	}
	
	/**
	 * Is the current node a root node?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_root()
	{
		return ($this->{$this->left_column} === 1);
	}
	
	/**
	 * Returns the root node.
	 *
	 * @access public
	 * @return ORM_MPTT
	 */
	public function root($scope = NULL)
	{
		if ($scope === NULL && $this->loaded)
		{
			$scope = $this->{$this->scope_column};
		}
		elseif ($scope === NULL && ! $this->loaded)
		{
			return FALSE;
		}
		
		return ORM_MPTT::factory($this->object_name)->where(array($this->left_column => 1, $this->scope_column => $scope))->find();
	}
	
	/**
	 * Returns the parent of the current node.
	 *
	 * @access public
	 * @return ORM_MPTT
	 */
	public function parent()
	{
		return $this->parents()->where($this->level_column, $this->{$this->level_column} - 1)->find();
	}
	
	/**
	 * Returns the parents of the current node.
	 *
	 * @access public
	 * @param bool $root include the root node?
	 * @param string $direction direction to order the left column by.
	 * @return ORM_MPTT
	 */
	public function parents($root = TRUE, $direction = 'ASC')
	{
		$parents =  ORM_MPTT::factory($this->object_name)
			->where(array(
				$this->left_column.' <=' => $this->{$this->left_column},
				$this->right_column.' >=' => $this->{$this->right_column},
				$this->primary_key.' <>' => $this->{$this->primary_key},
				$this->scope_column.' =' => $this->{$this->scope_column},
			))
			->orderby($this->left_column, $direction);
		
		if ( ! $root)
		{
			$parents->where($this->left_column.' !=', 1);
		}
			
		return $parents;
	}
	
	/**
	 * Returns the children of the current node.
	 *
	 * @access public
	 * @param bool $self include the current loaded node?
	 * @param string $direction direction to order the left column by.
	 * @return ORM_MPTT
	 */
	public function children($self = FALSE, $direction = 'ASC')
	{
		$levels = $self ? array($this->{$this->level_column} + 1, $this->{$this->level_column}) : array($this->{$this->level_column} + 1);
		return $this->descendants($self, $direction)->in($this->level_column, $levels);
	}
	
	/**
	 * Returns the descendants of the current node.
	 *
	 * @access public
	 * @param bool $self include the current loaded node?
	 * @param string $direction direction to order the left column by.
	 * @return ORM_MPTT
	 */
	public function descendants($self = FALSE, $direction = 'ASC')
	{
		$left_operator = $self ? '>=' : '>';
		$right_operator = $self ? '<=' : '<';
			
		return ORM_MPTT::factory($this->object_name)
			->where(array(
				$this->left_column.' '.$left_operator => $this->{$this->left_column},
				$this->right_column.' '.$right_operator => $this->{$this->right_column},
				$this->scope_column => $this->{$this->scope_column},
			))
			->orderby($this->left_column, $direction);
	}
	
	/**
	 * Returns the siblings of the current node
	 *
	 * @access public
	 * @param bool $self include the current loaded node?
	 * @param string $direction direction to order the left column by.
	 * @return ORM_MPTT
	 */
	public function siblings($self = FALSE, $direction = 'ASC')
	{
		$siblings = ORM_MPTT::factory($this->object_name)
			->where(array(
					$this->left_column.' >' => $this->parent->{$this->left_column},
					$this->right_column.' <' => $this->parent->{$this->right_column},
					$this->scope_column.' =' => $this->{$this->scope_column},
					$this->level_column => $this->{$this->level_column}
				))
				->orderby($this->left_column, $direction);
		
		if ( ! $self)
		{
			$siblings->where($this->primary_key.' <> ', $this->{$this->primary_key});
		}
		
		return $siblings;
	}
	
	/**
	 * Returns leaves under the current node.
	 *
	 * @access public
	 * @return ORM_MPTT
	 */
	public function leaves()
	{
		return ORM_MPTT::factory($this->object_name)
			->where('`'.$this->left_column.'` = (`'.$this->right_column.'` - 1)')
			->where($this->left_column.' >= ', $this->{$this->left_column})
			->where($this->right_column.' <= ', $this->{$this->right_column})
			->where($this->scope_column.' = ', $this->{$this->scope_column})
			->orderby($this->left_column, 'ASC');
	}
	
	/**
	 * Get Size
	 *
	 * @access protected
	 * @return integer
	 */
	protected function get_size()
	{
		return ($this->{$this->right_column} - $this->{$this->left_column}) + 1;
	}

	/**
	 * Create a gap in the tree to make room for a new node
	 *
	 * @access private
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	private function create_space($start, $size = 2)
	{
		// Update the left values, then the right.
		$this->db->query('UPDATE '.$this->table_name.' SET `'.$this->left_column.'` = `'.$this->left_column.'` + '.$size.' WHERE `'.$this->left_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column});
		$this->db->query('UPDATE '.$this->table_name.' SET `'.$this->right_column.'` = `'.$this->right_column.'` + '.$size.' WHERE `'.$this->right_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column});
	}
	
	/**
	 * Closes a gap in a tree. Mainly used after a node has
	 * been removed.
	 *
	 * @access private
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	private function delete_space($start, $size = 2)
	{
		// Update the left values, then the right.
		$this->db->query('UPDATE '.$this->table_name.' SET `'.$this->left_column.'` = `'.$this->left_column.'` - '.$size.' WHERE `'.$this->left_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column});
		$this->db->query('UPDATE '.$this->table_name.' SET `'.$this->right_column.'` = `'.$this->right_column.'` - '.$size.' WHERE `'.$this->right_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column});
	}
	
	protected function insert($target, $copy_left_from, $left_offset, $level_offset)
	{
		// Insert should only work on new nodes.. if its already it the tree it needs to be moved!
		if ($this->loaded)
			return FALSE;
		
		$this->lock();
		
		if ( ! $target instanceof $this)
		{
			$target = ORM_MPTT::factory($this->object_name, $target);
		}
		else
		{
			$target->reload(); // Ensure we're using the latest version of $target
		}
		
		$this->{$this->left_column}  = $target->{$copy_left_from} + $left_offset;
		$this->{$this->right_column} = $this->{$this->left_column} + 1;
		$this->{$this->level_column} = $target->{$this->level_column} + $level_offset;
		$this->{$this->scope_column} = $target->{$this->scope_column};
		
		$this->create_space($this->{$this->left_column});
		
		parent::save();
		
		$this->unlock();
		
		return $this;
	}
	
	/**
	 * Inserts a new node to the left of the target node.
	 *
	 * @access public
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function insert_as_first_child($target)
	{
		return $this->insert($target, $this->left_column, 1, 1);
	}
	
	/**
	 * Inserts a new node to the right of the target node.
	 *
	 * @access public
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function insert_as_last_child($target)
	{
		return $this->insert($target, $this->right_column, 0, 1);
	}

	/**
	 * Inserts a new node as a previous sibling of the target node.
	 *
	 * @access public
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function insert_as_prev_sibling($target)
	{
		return $this->insert($target, $this->left_column, 0, 0);
	}

	/**
	 * Inserts a new node as the next sibling of the target node.
	 *
	 * @access public
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function insert_as_next_sibling($target)
	{
		return $this->insert($target, $this->right_column, 1, 0);
	}
	
	/**
	 * Removes a node and it's descendants.
	 *
	 * $usless_param prevents a strict error that breaks PHPUnit like hell!
	 * @access public
	 * @param bool $descendants remove the descendants?
	 */
	public function delete($usless_param = NULL)
	{
		$this->lock();
		
		$this->db->delete($this->table_name, '`'.$this->left_column.'` BETWEEN '.$this->{$this->left_column}.' AND '.$this->{$this->right_column}.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column});
		$this->delete_space($this->{$this->left_column}, $this->get_size());

		$this->unlock();
	}

	/**
	 * Overloads the select_list method to
	 * support indenting.
	 *
	 * @param string $key first table column.
	 * @param string $val second table column.
	 * @param string $indent character used for indenting.
	 * @return array
	 */
	public function select_list($key = NULL, $val = NULL, $indent = NULL)
	{
		if (is_string($indent))
		{
			if ($key === NULL)
			{
				// Use the default key
				$key = $this->primary_key;
			}
	
			if ($val === NULL)
			{
				// Use the default value
				$val = $this->primary_val;
			}
			
			$result = $this->load_result(TRUE);
			
			$array = array();
			foreach ($result as $row)
			{
				$array[$row->$key] = str_repeat($indent, $row->{$this->level_column}).$row->$val;
			}
			
			return $array;
		}

		return parent::select_list($key, $val);
	}
	

	
	/**
	 * Move to First Child
	 *
	 * Moves the current node to the first child of the target node.
	 *
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function move_to_first_child($target)
	{
		$this->lock();
		
		// Move should only work on nodes that are already in the tree.. if its not already it the tree it needs to be inserted!
		if (!$this->loaded)
			return FALSE;

		// Make sure we have the most uptodate version of this AFTER we lock
		$this->reload(); // This should *probably* go into $this->lock();
		
		if ( ! $target instanceof $this)
		{
			$target = ORM_MPTT::factory($this->table_name, $target);
		}
		
		// Stop $this being moved into a descendant
		if ($target->is_descendant($this))
		{
			$this->unlock();
			return FALSE;
		}
		
		$new_left = $target->{$this->left_column} + 1;
		$level_offset = $target->{$this->level_column} - $this->{$this->level_column} + 1;
		$this->move($new_left, $level_offset, $target->{$this->scope_column});
		$this->unlock();

		return $this;
	}
	
	/**
	 * Move to Last Child
	 *
	 * Moves the current node to the last child of the target node.
	 *
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function move_to_last_child($target)
	{
		// Move should only work on nodes that are already in the tree.. if its not already it the tree it needs to be inserted!
		if (!$this->loaded)
			return FALSE;
			
		$this->lock();
		$this->reload(); // Make sure we have the most upto date version of this AFTER we lock
		
		if ( ! $target instanceof $this)
		{
			$target = ORM_MPTT::factory($this->table_name, $target);
		}
		
		// Stop $this being moved into a descendant
		if ($target->is_descendant($this))
		{
			$this->unlock();
			return FALSE;
		}
		
		$new_left = $target->{$this->right_column};
		$level_offset = $target->{$this->level_column} - $this->{$this->level_column} + 1;
		$this->move($new_left, $level_offset, $target->{$this->scope_column});
		$this->unlock();
		
		return $this;
	}
	
	/**
	 * Move to Previous Sibling.
	 *
	 * Moves the current node to the previous sibling of the target node.
	 *
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function move_to_prev_sibling($target)
	{
		// Move should only work on nodes that are already in the tree.. if its not already it the tree it needs to be inserted!
		if (!$this->loaded)
			return FALSE;
		
		$this->lock();
		$this->reload(); // Make sure we have the most upto date version of this AFTER we lock
		
		if ( ! $target instanceof $this)
		{
			$target = ORM_MPTT::factory($this->table_name, $target);
		}
		
		// Stop $this being moved into a descendant
		if ($target->is_descendant($this))
		{
			$this->unlock();
			return FALSE;
		}
		
		$new_left = $target->{$this->left_column};
		$level_offset = $target->{$this->level_column} - $this->{$this->level_column};
		$this->move($new_left, $level_offset, $target->{$this->scope_column});
		$this->unlock();
		
		return $this;
	}
	
	/**
	 * Move to Next Sibling.
	 *
	 * Moves the current node to the next sibling of the target node.
	 *
	 * @param ORM_MPTT|integer $target target node id or ORM_MPTT object.
	 * @return ORM_MPTT
	 */
	public function move_to_next_sibling($target)
	{
		// Move should only work on nodes that are already in the tree.. if its not already it the tree it needs to be inserted!
		if (!$this->loaded)
			return FALSE;
		
		$this->lock();
		$this->reload(); // Make sure we have the most upto date version of this AFTER we lock
		
		if ( ! $target instanceof $this)
		{
			$target = ORM_MPTT::factory($this->table_name, $target);
		}
		
		// Stop $this being moved into a descendant
		if ($target->is_descendant($this))
		{
			$this->unlock();
			return FALSE;
		}
		
		$new_left = $target->{$this->right_column} + 1;
		$level_offset = $target->{$this->level_column} - $this->{$this->level_column};
		$this->move($new_left, $level_offset, $target->{$this->scope_column});
		$this->unlock();
		
		return $this;
	}
	
	/**
	 * Move
	 *
	 * @param integer $new_left left value for the new node position.
	 * @param integer $level_offset
	 */
	protected function move($new_left, $level_offset, $new_scope)
	{
		$this->lock();
		
		$size = $this->get_size();
		
		$this->create_space($new_left, $size);

		$this->reload();
		
		$offset = ($new_left - $this->{$this->left_column});
		
		// Update the values.
		$this->db->query('UPDATE '.$this->table_name.' SET `'.$this->left_column.'` = `'.$this->left_column.'` + '.$offset.', `'.$this->right_column.'` = `'.$this->right_column.'` + '.$offset.'
		, `'.$this->level_column.'` = `'.$this->level_column.'` + '.$level_offset.'
		, `'.$this->scope_column.'` = '.$new_scope.'
		WHERE `'.$this->left_column.'` >= '.$this->{$this->left_column}.' AND `'.$this->right_column.'` <= '.$this->{$this->right_column}.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column});
		
		$this->delete_space($this->{$this->left_column}, $size);
		
		$this->unlock();
	}
	
	/**
	 *
	 * @access public
	 * @param $column - Which field to get.
	 * @return mixed
	 */
	public function __get($column)
	{
		switch ($column)
		{
			case 'parent':
				return $this->parent();
			case 'parents':
				return $this->parents()->find_all();
			case 'children':
				return $this->children()->find_all();
			case 'first_child':
				return $this->children()->find();
			case 'last_child':
				return $this->children('DESC')->find();
			case 'siblings':
				return $this->siblings()->find_all();
			case 'root':
				return $this->root();
			case 'leaves':
				return $this->leaves()->find_all();
			case 'descendants':
				return $this->descendants()->find_all();
			default:
				return parent::__get($column);
		}
	}
	
	/**
	 * Verify the tree is in good order
	 *
	 * This functions speed is irrelevant - its really only for debugging and unit tests
	 *
	 * @todo Look for any nodes no longer contained by the root node.
	 * @todo Ensure every node has a path to the root via ->parents();
	 * @access public
	 * @return boolean
	 */
	public function verify_tree()
	{
		foreach ($this->get_scopes() as $scope)
		{
			if ( ! $this->verify_scope($scope->scope))
				return FALSE;
		}
		return TRUE;
	}
	
	private function get_scopes()
	{
		// TODO... redo this so its proper :P and open it public
		// used by verify_tree()
		return $this->db->query('SELECT DISTINCT(`'.$this->scope_column.'`) from `'.$this->table_name.'`');
	}
	
	
	public function verify_scope($scope)
	{
		$root = $this->root($scope);
		
		$end = $root->{$this->right_column};
		
		// Find nodes that have slipped out of bounds.
		$result = $this->db->query('SELECT count(*) as count FROM `'.$this->table_name.'` WHERE `'.$this->scope_column.'` = '.$root->scope.' AND (`'.$this->left_column.'` > '.$end.' OR `'.$this->right_column.'` > '.$end.')');
		if ($result[0]->count > 0)
			return FALSE;
		
		// Find nodes that have the same left and right value
		$result = $this->db->query('SELECT count(*) as count FROM `'.$this->table_name.'` WHERE `'.$this->scope_column.'` = '.$root->scope.' AND `'.$this->left_column.'` = `'.$this->right_column.'`');
		if ($result[0]->count > 0)
			return FALSE;
		
		// Find nodes that right value is less than the left value
		$result = $this->db->query('SELECT count(*) as count FROM `'.$this->table_name.'` WHERE `'.$this->scope_column.'` = '.$root->scope.' AND `'.$this->left_column.'` > `'.$this->right_column.'`');
		if ($result[0]->count > 0)
			return FALSE;
		
		// Make sure no 2 nodes share a left/right value
		$i = 1;
		while ($i <= $end)
		{
			$result = $this->db->query('SELECT count(*) as count FROM `'.$this->table_name.'` WHERE `'.$this->scope_column.'` = '.$root->scope.' AND (`'.$this->left_column.'` = '.$i.' OR `'.$this->right_column.'` = '.$i.')');
			
			if ($result[0]->count > 1)
				return FALSE;
				
			$i++;
		}
		
		// Check to ensure that all nodes have a "correct" level
		//TODO
		
		return TRUE;
	}
	
	/**
	 * Generates the HTML for this node's descendants
	 *
	 * @param string $style pagination style.
	 * @param boolean $self include this node or not.
	 * @param string $direction direction to order the left column by.
	 * @return View
	 */
	public function render_descendants($style = NULL, $self = FALSE, $direction = 'ASC')
	{
		$nodes = $this->descendants($self, $direction)->find_all();
		
		if ($style === NULL)
		{
			$style = $this->style;
		}

		return View::factory($this->directory.$style, array('nodes' => $nodes,'level_column' => $this->level_column));
	}
	
	/**
	 * Generates the HTML for this node's children
	 *
	 * @param string $style pagination style.
	 * @param boolean $self include this node or not.
	 * @param string $direction direction to order the left column by.
	 * @return View
	 */
	public function render_children($style = NULL, $self = FALSE, $direction = 'ASC')
	{
		$nodes = $this->children($self, $direction)->find_all();
		
		if ($style === NULL)
		{
			$style = $this->style;
		}

		return View::factory($this->directory.$style, array('nodes' => $nodes,'level_column' => $this->level_column));
	}
	
}
