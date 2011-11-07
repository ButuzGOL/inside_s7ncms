<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo html::stylesheet(array(theme::$css.'/ui/jquery-ui.css', theme::$css.'/layout.css', theme::$css.'/ui.tabs.css'), 'screen'); ?>
	<?php echo html::script(array('vendor/jquery/jquery.js','vendor/jquery/jquery-ui.min.js','vendor/jquery/ui/ui.tree.js',theme::$js.'/stuff.js'), FALSE); ?>
	<?php echo $head ?>
</head>

<body>
	<div id="dialog" style="display: none;"><?php echo __('Are you sure want to delete?'); ?></div>
	<div id="header">
		S7Nadmin
		<div class="info">
			<?php echo __('Logged in as %username', array('%username' => Auth::instance()->get_user()->username)) ?> | <?php echo html::anchor('/', __('Visit site')) ?> | <?php echo html::anchor('admin/auth/logout', __('Logout')); ?>
		</div>
	</div>

	<div id="navigation">
		<ul>
			<li><?php echo html::anchor('admin/page', 'Pages'); ?></li>
			<?php echo menus::modules(); ?>
			<li><?php echo html::anchor('admin/modules', 'Modules'); ?></li>
			<li><?php echo html::anchor('admin/user', 'Users'); ?></li>
			<li><?php echo html::anchor('admin/settings', 'Settings'); ?></li>
		</ul>
	</div>

	<div id="main">
		<div id="title">
			<h2><?php echo $title ?></h2>
			<?php if ($searchbar): ?>
			    <?php echo form::open(NULL, array('id' => 'searchbar', 'method' => 'get')) ?>
			        <input name="q" value="<?php echo $searchvalue ?>" type="search" placeholder="<?php echo __('Filter by') ?>" autosave="s7nm.search" />
			    <?php echo form::close() ?>
			<?php endif ?>
		</div>

		<div id="left">
			<div id="sidebar">
				<?php if(isset($tasks) AND !empty($tasks)): ?>
					<h3><?php echo __('Tasks') ?></h3>
					<p>
						<?php foreach($tasks as $task): ?>
							<?php echo html::anchor($task[0], $task[1]); ?><br />
						<?php endforeach; ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div id="content">
			<?php if($message = message::info()): ?>
				<div id="info_message">
					<p><?php echo $message ?></p>
				</div>
			<?php endif; ?>
			<?php if($message = message::error()): ?>
				<div id="error_message">
					<p><?php echo $message ?></p>
				</div>
			<?php endif; ?>
			<?php echo $content ?>
		</div>
	</div>
</body>
</html>
