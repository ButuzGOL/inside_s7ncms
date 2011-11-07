<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php if($modules): ?>
<h1><?php echo __('Available Modules') ?></h1>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td><?php echo __('Module') ?></td>
			<td><?php echo __('Action') ?></td>
			<td class="delete">&nbsp;</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($modules as $module => $version): ?>
		<tr>
			<td><?php echo $module ?></td>
			<td>
			<?php if (module::installed($module)): ?>
				<?php if (module::active($module)): ?>
					<?php echo html::anchor('admin/modules/status/'.$module.'/off', __('disable')); ?>
				<?php else: ?>
					<?php echo html::anchor('admin/modules/status/'.$module.'/on', __('enable')); ?>
				<?php endif ?>
			<?php else: ?>
				<?php echo html::anchor('admin/modules/install/'.$module, __('install')); ?>
			<?php endif ?>
			</td>
			<td class="delete">
			<?php if (module::installed($module)) echo html::anchor('admin/modules/uninstall/'.$module, html::image(
				theme::$images.'/delete.png',
				array(
					'alt' => __('Uninstall Module'),
					'title' => __('Uninstall Module')
					)), array('class' => 'confirm')); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>
