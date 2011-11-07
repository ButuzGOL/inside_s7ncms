<h1>The End</h1>
<p>You successfully installed S7Ncms!</p>
<p>Please write down your password: <strong><?php echo $password ?></strong>. You can login to the administration panel with the username <em>admin</em>.</p>
<p>
	<?php echo html::anchor('', 'Your Website') ?><br />
	<?php echo html::anchor(url::base(FALSE, 'http').'index.php/admin', 'Administration') ?>
</p>