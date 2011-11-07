<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<?php echo html::stylesheet(theme::$css.'/login.css', 'screen'); ?>
	<title>S7Ncms login</title>
	<script type="text/javascript">
        window.onload = function () {document.getElementById('username').focus();};
    </script>
</head>
<body>
<div id="login">
    <div id="logo"><?php echo html::image('themes/default/images/s7n_logo.png') ?></div>
    <div id="formular">
        <div id="message">
        <?php if ( ! empty($error)): ?>
        	<?php echo $error ?>
        <?php endif ?>
        </div>

		<?php echo form::open() ?>
		    <?php echo $form->__formo; ?>
	        <p class="email">
	        	<?php echo form::label($form->username->name, $form->username->label) ?><br />
	        	<?php echo empty($form->username->error) ? form::input($form->username->name, $form->username->value) : form::input($form->username->name, $form->username->value, 'class="error"') ?>
	        	<?php if ( ! empty($form->username->error)): ?><br /><span class="error"><?php echo $form->username->error ?></span><?php endif ?>
	        </p>
	        <p class="password">
	        	<?php echo form::label($form->password->name, $form->password->label) ?><br />
	        	<?php echo empty($form->password->error) ? form::password($form->password->name) : form::password($form->password->name, '', 'class="error"') ?>
	        	<?php if ( ! empty($form->password->error)): ?><br /><span class="error"><?php echo $form->password->error ?></span><?php endif ?>
	        </p>
	        <p class="submit">
	        	<?php echo form::submit($form->submit->name, $form->submit->label) ?>
	        </p>
        <?php echo form::close() ?>
    </div>
</div>
</body>
</html>
