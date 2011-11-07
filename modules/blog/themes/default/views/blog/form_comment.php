<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h3 id="respond">
	<?php echo __('Your comment')?>:
</h3>

<?php echo form::open(NULL, array('id' => 'commentform')) ?>
	<?php echo $form->__formo; ?>
	<p>
		<?php echo empty($form->author->error) ? form::input('author', $form->author->value) : form::input('author', $form->author->value, 'class="error"') ?>
		<?php echo form::label('author', $form->author->label) ?>
		<?php if ( ! empty($form->author->error)): ?><br /><span class="error"><?php echo $form->author->error ?></span><?php endif ?>
	</p>
	<p>
		<?php echo empty($form->email->error) ? form::input('email', $form->email->value) : form::input('email', $form->email->value, 'class="error"') ?>
		<?php echo form::label('email', $form->email->label) ?>
		<?php if ( ! empty($form->email->error)): ?><br /><span class="error"><?php echo $form->email->error ?></span><?php endif ?>
	</p>
	<p>
		<?php echo empty($form->url->error) ? form::input('url', $form->url->value) : form::input('url', $form->url->value, 'class="error"') ?>
		<?php echo form::label('url', $form->url->label) ?>
		<?php if ( ! empty($form->url->error)): ?><br /><span class="error"><?php echo $form->url->error ?></span><?php endif ?>
	</p>
	<p>
		<?php echo empty($form->content->error) ? form::textarea('content', $form->content->value) : form::textarea('content', $form->content->value, 'class="error"') ?>
		<?php if ( ! empty($form->content->error)): ?><br /><span class="error"><?php echo $form->content->error ?></span><?php endif ?>
	</p>
	<?php if (config::get('blog.enable_captcha') === 'yes'):?>
	<p>
		<?php echo $form->security->render() ?><br />
		<?php if ( ! empty($form->security->error)): ?><br /><span class="error"><?php echo $form->security->error ?></span><?php endif ?>
	</p>
	<?php endif; ?>
	<p>
		<?php echo form::submit('submit', $form->submit->value) ?>
	</p>
<?php echo form::close() ?>
