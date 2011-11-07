<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <?php echo html::stylesheet(theme::$css.'/layout.css', 'screen'); ?>
		<?php echo $head ?>
	</head>
	<body>
		<div id="header">
			<div class="widthfix">
				<div class="logo">
					<h1><?php echo html::anchor('/', config::get('s7nm.site_title')) ?></h1>
				</div>
				<div class="clear"></div>
				<?php echo Menu::instance(); ?>
			</div>
		</div>
		<div id="content">
			<div id="article">
				<div class="entry">
					<?php echo $content; ?>
				</div>
			</div>
			<div id="sidebar">
				<?php echo Widget::factory('Submenu') ?>
				<?php echo Sidebar::instance() ?>
			</div>
			<div class="clear"></div>
		</div>
		<div id="bottom">
		</div>
		<div id="footer">
			<div class="widthfix">
				© 2008 Your Name, powered by <a href="http://www.s7n.de/">S7Ncms</a>, theme by <a href="http://hellowiki.com/about/">Fen</a>
			</div>
		</div>
		<div class="none"></div>
	</body>
</html>
