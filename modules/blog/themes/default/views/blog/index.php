<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<?php foreach ($posts as $post): ?>
	<div class="entry">
		<div class="entrytitle">
			<h2><?php echo html::anchor(url::current_site($post->uri), $post->title) ?></h2>
			<h3 class="date">
				<?php echo date("Y-m-d H:i:s", $post->date) ?> (<?php echo __n('One comment', '%count comments', $post->comment_count) ?>)
			</h3>
		</div>
		<?php echo $post->content ?>
	</div>
<?php endforeach; ?>

<div class="navigation">
	<?php if (isset($pagination)) echo $pagination ?>
</div>
