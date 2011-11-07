<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<script type="text/javascript">
$(document).ready(function(){

	$("ul.sortable").tree({
		sortOn: "li",
		dropOn: ".folder",
		dropHoverClass: "hover",
		handle: ".movehandle"
	});

	$('#save_sort').click(function(){
		$.post('<?php echo url::site('admin/page/save_tree') ?>', '&tree='+$("ul.sortable").tree('serialize'), function(data, status) {
			window.location.reload();
		});
	});

});
</script>
<div class="box">
	<h3><?php echo __('Page Order') ?></h3>
	<div class="inside">
		<ul class="sortable">
		<?php
		$level = 0;

		foreach ($pages as $node)
		{
			$has_children = (($node->rgt - $node->lft - 1) > 0 );
			$id = 'page-'.$node->id;

			$value = View::factory('page/index_tree_value', array('page' => $node));

			if($level > $node->lvl) {
				echo str_repeat("</ul></li>\n",($level - $node->lvl));
				echo '<li id="'.$id.'">'.$value."\n";
			} else
				echo '<li id="'.$id.'">'.$value."\n";
			if($has_children)
			    echo '<ul>'."\n";
		
			$level = $node->lvl;
		}
		echo str_repeat("</ul></li>\n",$level);
		?>
		</ul>
	</div>
</div>
<p>
<button id="save_sort"><?php echo __('Save page order') ?></button>
</p>
