<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php if($users): ?>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td><?php echo __('Username') ?></td>
			<td><?php echo __('Email') ?></td>
			<td class="delete">&nbsp;</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($users as $user): ?>
		<tr>
			<td><?php echo html::anchor('admin/user/edit/'.$user->id, $user->username) ?></td>
			<td><?php echo $user->email ?></td>
			<td class="delete">
			<?php echo html::anchor('admin/user/delete/'.$user->id, html::image(
				theme::$images.'/delete.png',
				array(
					'alt' => __('Delete User'),
					'title' => __('Delete User')
				)), array('class' => 'confirm')) ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>
