<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo form::open(); ?>
    <?php echo $form->__formo; ?>
    <div class="box">
	    <h3><?php echo __('General Settings') ?></h3>
	    <div class="inside">
		    <p><?php echo form::label($form->site_title->name, $form->site_title->label).form::input($form->site_title->name, $form->site_title->value); ?></p>
		    <p><?php echo form::label($form->theme->name, $form->theme->label).form::dropdown($form->theme->name, $form->theme->values, $form->theme->value); ?></p>
		    <p><?php echo form::label($form->admintheme->name, $form->admintheme->label).form::dropdown($form->admintheme->name, $form->admintheme->values, $form->admintheme->value); ?></p>
	    </div>
    </div>
    <p><?php echo form::submit($form->submit->name, $form->submit->label); ?></p>
<?php echo form::close(); ?>
