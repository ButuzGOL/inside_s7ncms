<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="entry">
	<div class="entrytitle">
		<h2><?php echo html::anchor(url::current_site($post->uri), $post->title) ?></h2>
		<h3 class="date">
			<?php echo date("Y-m-d H:i:s", $post->date) ?>
		</h3>
	</div>
	<?php echo $post->content ?>
</div>
<div id="comments">
<?php if (count($comments) > 0): ?>
	<h3>
		<?php echo __n('One comment', '%count comments', $post->comment_count) ?>
	</h3>
	<ol class="commentlist">
		<?php $counter = 1; foreach ($comments as $comment): ?>
		<li class="alt">
			<div class="commentcount"><?php echo $counter++ ?></div>
			<cite><?php echo html::specialchars($comment->author) ?></cite><br />
			<small class="commentmetadata"><?php echo strftime('%e. %B %Y, %H:%M', strtotime($comment->date)) ?></small>
			<?php echo nl2br(html::specialchars($comment->content)) ?>
		</li>
		<?php endforeach ?>
	</ol>
<?php endif ?>

<?php echo $form ?>
</div>
