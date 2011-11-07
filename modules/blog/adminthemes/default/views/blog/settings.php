<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo form::open() ?>
<?php echo $form->__formo ?>
<div class="box">
	<h3><?php echo __('Blog Settings') ?></h3>
	<div class="inside">
		<p>
			<?php echo form::label($form->items_per_page->name, $form->items_per_page->label) ?>
			<?php echo empty($form->items_per_page->error) ? form::input($form->items_per_page->name, $form->items_per_page->value) : form::input($form->items_per_page->name, $form->items_per_page->value, 'class="error"') ?>
			<?php if ( ! empty($form->items_per_page->error)): ?><br /><span class="error"><?php echo $form->items_per_page->error ?></span><?php endif ?>
		</p>
		<p>
			<?php echo form::checkbox($form->enable_captcha->name, $form->enable_captcha->value, $form->enable_captcha->checked) ?> <?php echo __('Enable captcha') ?><br/>
			<?php echo form::checkbox($form->enable_tagcloud->name, $form->enable_tagcloud->value, $form->enable_tagcloud->checked) ?> <?php echo __('Enable tag cloud') ?><br/>
			<?php echo form::checkbox($form->comment_status->name, $form->comment_status->value, $form->comment_status->checked) ?> <?php echo __('Enable comments') ?>
		</p>
	</div>
</div>

<p><?php echo form::submit($form->submit->name, $form->submit->label); ?></p>

<?php echo form::close(); ?>
