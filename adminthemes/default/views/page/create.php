<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo form::open() ?>
<?php echo $form->__formo; ?>

<div class="box">
	<h3><?php echo __('Page Information') ?></h3>
	<div class="inside">
		<p>
			<?php echo form::label($form->type->name, __('Redirect page or load Module')) ?>
			<?php
				foreach ($form->type->elements as $key => $value)
				{
					switch ($value) {
						case 'module':
							if ( empty($form->module)) continue;
							echo form::radio($form->type->name, $value).' '.$form->type->$key->label.': ';
							echo form::dropdown($form->module->name, $form->module->values, $form->module->value).'<br />';
							break;
						
						case 'redirect':
						    if ( empty($form->redirect)) continue;
							echo form::radio($form->type->name, $value).' '.$form->type->$key->label.': ';
							echo form::dropdown($form->redirect->name, $form->redirect->values, $form->redirect->value);
							break;
							
						default:
							echo form::radio($form->type->name, $value, true).' '.$form->type->$key->label;
							echo '<br />';
							break;
					}
				}
			?>
	</div>
</div>

<?php foreach (Kohana::config('locale.languages') as $key => $value): ?>
<div class="box">
	<h3><?php echo __('Content') ?> <small>(<?php echo $value['name'] ?>)</small></h3>
	<div class="inside">
		<p>
			<?php echo form::label($form->{'title_'.$key}->name, $form->{'title_'.$key}->label) ?>
			<?php echo empty($form->{'title_'.$key}->error) ? form::input($form->{'title_'.$key}->name, $form->{'title_'.$key}->value) : form::input($form->{'title_'.$key}->name, $form->{'title_'.$key}->value, 'class="error"') ?>
			<?php if ( ! empty($form->{'title_'.$key}->error)): ?><br /><span class="error"><?php echo $form->{'title_'.$key}->error ?></span><?php endif ?>
		</p>
		<p><?php echo form::label($form->{'content_'.$key}->name, $form->{'content_'.$key}->label).form::textarea($form->{'content_'.$key}->name, $form->{'content_'.$key}->value) ?></p>
	</div>
</div>
<?php endforeach; ?>

<p><?php echo form::submit($form->submit->name, $form->submit->label); ?></p>

<?php echo form::close(); ?>

