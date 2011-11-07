<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo html::stylesheet('adminthemes/default/css/layout') ?>
    <?php echo html::script('vendor/jquery/jquery') ?>
    <?php echo html::script('vendor/jquery/jquery-ui.min') ?>
    <?php echo html::script('adminthemes/default/js/stuff') ?>
</head>

<body>
	<div id="header">
		S7Ncms Installer
	</div>

	<div id="navigation">
	</div>

	<div id="main">
		<div id="title">
			<h2><?php echo $title ?></h2>
		</div>

		<div id="left">
			<div id="sidebar">
			</div>
		</div>

		<div id="content">
			<?php if ( ! empty($error)): ?>
				<div id="error_message">
					<p><?php echo $error ?></p>
				</div>
			<?php endif; ?>
			<?php echo $content ?>
		</div>
	</div>
</body>
</html>
