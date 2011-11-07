<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td><?php echo __('Author') ?></td>
			<td><?php echo __('Comment') ?></td>
			<td><?php echo __('Date') ?></td>
			<td class="delete">&nbsp;</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php if($comments) foreach($comments as $comment): ?>
		<tr>
			<td><?php echo html::specialchars($comment->author) ?></td>
			<td><?php echo html::specialchars(text::limit_chars($comment->content, 70)) ?></td>
			<td><?php echo date("Y-m-d H:i:s", $comment->date) ?></td>
			<td class="delete">(<?php echo html::anchor('admin/blog/comments/edit/'.$comment->id, __('edit')) ?>)
			<?php echo html::anchor('admin/blog/comments/delete/'.$comment->id, html::image(
				theme::$images.'/delete.png',
				array(
					'alt' => __('Delete Page'),
					'title' => __('Delete Page')
					)
				)) ?>
			</td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
