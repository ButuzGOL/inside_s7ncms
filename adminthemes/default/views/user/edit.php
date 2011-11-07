<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo form::open() ?>
<?php echo $form->__formo; ?>

<div class="box">
	<h3><?php echo __('Edit user') ?></h3>
	<div class="inside">
		<p>
			<?php echo form::label($form->username->name, $form->username->label) ?>
			<?php echo empty($form->username->error) ? form::input($form->username->name, $form->username->value) : form::input($form->username->name, $form->username->value, 'class="error"') ?>
			<?php if ( ! empty($form->username->error)): ?><br /><span class="error"><?php echo $form->username->error ?></span><?php endif ?>
		</p>
		<p>
			<?php echo form::label($form->email->name, $form->email->label) ?>
			<?php echo empty($form->email->error) ? form::input($form->email->name, $form->email->value) : form::input($form->email->name, $form->email->value, 'class="error"') ?>
			<?php if ( ! empty($form->email->error)): ?><br /><span class="error"><?php echo $form->email->error ?></span><?php endif ?>
		</p>
		<p>
			<?php echo form::label($form->password->name, $form->password->label) ?>
			<?php echo empty($form->password->error) ? form::password($form->password->name, $form->password->value) : form::password($form->password->name, $form->password->value, 'class="error"') ?>
			<?php if ( ! empty($form->password->error)): ?><br /><span class="error"><?php echo $form->password->error ?></span><?php endif ?>
		</p>
		<p>
			<?php echo form::label($form->password_confirm->name, $form->password_confirm->label) ?>
			<?php echo empty($form->password_confirm->error) ? form::password($form->password_confirm->name, $form->password_confirm->value) : form::password($form->password_confirm->name, $form->password_confirm->value, 'class="error"') ?>
			<?php if ( ! empty($form->password_confirm->error)): ?><br /><span class="error"><?php echo $form->password_confirm->error ?></span><?php endif ?>
		</p>
	</div>
</div>

<p><?php echo form::submit($form->submit->name, $form->submit->label); ?></p>

<?php echo form::close(); ?>
