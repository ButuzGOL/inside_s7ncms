<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="folder">
    <div style="float: left;">
    <span class="movehandle"></span>
    <?php echo html::anchor('admin/page/edit/'.$page->id, '<span>'.$page->title.'</span>'); ?>
    </div>
    <div class="delete" style="position:absolute;right:10px;">
    <?php echo html::anchor('admin/page/delete/'.$page->id, html::image(
		theme::$images.'/delete.png',
		array(
			'alt' => __('Delete Page'),
			'title' => __('Delete Page')
		)), array('class' => 'confirm'))
	?>
    </div>
</div>
