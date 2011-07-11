<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div id="freshcomments">
	<ul class="postlist">
		<?php foreach ($content->freshcomments as $post): ?>
		<li class="postlist-meta">
			<h3><a href="<?php echo $post['post']->permalink; ?>"><?php echo $post['post']->title_out; ?></a></h3>
			<p><a href="<?php echo $post['post']->permalink; ?>#comments" class="comment-count" title="<?php printf(_n('%1$d comment', '%1$d comments', $post['post']->comments->approved->comments->count), $post['post']->comments->approved->comments->count); ?>"><?php printf(_n('%1$d comment', '%1$d comments', $post['post']->comments->approved->comments->count), $post['post']->comments->approved->comments->count); ?></a></p>
		</li>
		<?php endforeach; ?>
	</ul>
</div>