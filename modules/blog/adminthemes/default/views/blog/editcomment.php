<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo form::open() ?>
<?php echo $form->__formo ?>
<div class="box">
	<h3><?php echo __('Edit Comment') ?></h3>
	<div class="inside">
		<p><?php echo form::label($form->author->name, $form->author->label).form::input($form->author->name, $form->author->value); ?></p>
		<p><?php echo form::label($form->email->name, $form->email->label).form::input($form->email->name, $form->email->value); ?></p>
		<p><?php echo form::label($form->url->name, $form->url->label).form::input($form->url->name, $form->url->value); ?></p>
		<p><?php echo form::label($form->content->name, $form->content->label).form::textarea($form->content->name, $form->content->value); ?></p>
	</div>
</div>

<p><?php echo form::submit($form->submit->name, $form->submit->label); ?></p>

<?php echo form::close(); ?>
