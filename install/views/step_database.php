<?php defined("SYSPATH") or die("No direct script access.") ?>
<?php echo form::open(); ?>
<div class="box">
	<div class="inside">
		<p><?php echo form::label('username', 'Username').form::input('username', $form['username']) ?></p>
		<p><?php echo form::label('password', 'Password').form::input('password', $form['password']) ?></p>
		<p><?php echo form::label('hostname', 'Hostname').form::input('hostname', $form['hostname']) ?></p>
		<p><?php echo form::label('database', 'Database').form::input('database', $form['database']) ?></p>
	</div>
</div>

<p><?php echo form::submit('submit', 'Next') ?></p>
<?php echo form::close() ?>