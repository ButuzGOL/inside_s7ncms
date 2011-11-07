<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo form::open() ?>
<?php echo $form->__formo; ?>
<div class="box">
	<h3><?php echo __('New Blog Post') ?></h3>
	<div class="inside">
		<p>
			<?php echo form::label($form->title->name, $form->title->label) ?>
			<?php echo empty($form->title->error) ? form::input($form->title->name, $form->title->value) : form::input($form->title->name, $form->title->value, 'class="error"') ?>
			<?php if ( ! empty($form->title->error)): ?><br /><span class="error"><?php echo $form->title->error ?></span><?php endif ?>
		</p>
		<p><?php echo form::label($form->content->name, $form->content->label).form::textarea($form->content->name, $form->content->value) ?></p>
		<p><?php echo form::label($form->tags->name, $form->tags->label).form::input($form->tags->name, $form->tags->value) ?></p>
	</div>
</div>

<p><?php echo form::submit($form->submit->name, $form->submit->label) ?></p>

<?php echo form::close() ?>
