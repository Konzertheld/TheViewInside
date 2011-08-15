<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php
$usercanmoderate = (User::identify()->id && (User::identify()->can("manage_all_comments") || (User::identify()->can("manage_own_post_comments") && $post->id == User::identify()->id)));
if($usercanmoderate)
	$comments = $post->comments;
else
	$comments = $post->comments->moderated;
?>
<div id="comments">
	<h3 id="comments-headline">
		<?php if($comments->count)
		{
			printf(_n('%1$d comment so far', '%1$d comments so far', $post->comments->moderated->count), $post->comments->moderated->count);
		}
		else if(!$post->info->comments_disabled) _e('No comments yet. Start the discussion!', $theme->name);
		else _e('No comments');
		?>
	</h3>
	<?php if($comments->count)
	{
		$evenoddcomment = true;
		foreach ($comments as $comment)
		{
			?>
			<div id="comment-<?php echo $comment->id; ?>" class="comment<?php if($evenoddcomment) echo ' even'; else echo ' odd';?>">
				<?php $theme->comment_gravatar($comment); ?>
				<div class="comment-main">
					<div class="comment-meta">
						<?php printf(_t(' %s said at %s:', $theme->name),
						'<span class="comment-author">'.$theme->theme_comment_author_link($theme, $comment).'</span>',
						'<span><a href="'.$post->permalink.'#comment-'.$comment->id.'">'.$comment->date->format().'</a></span>'); ?>
					</div>
					<div class="comment-content">
						<?php echo $comment->content_out; ?>
						<?php if($comment->status == Comment::STATUS_UNAPPROVED || $comment->status == Comment::STATUS_SPAM): ?>
						<p class="newunapproved"><em><?php _e( 'This comment is awaiting moderation', $theme->name ) ;?></em></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php $evenoddcomment = !$evenoddcomment;
		}
	}
	?>
	<?php
	if($usercanmoderate && $post->comments->unapproved->count + $post->comments->spam->count > 0)
	{	
		?><p id="unapproved-comments"><?php
		printf(_n('%1$d unmoderated comment. ', '%1$d unmoderated comments. ', $post->comments->unapproved->count + $post->comments->spam->count), $post->comments->unapproved->count + $post->comments->spam->count);
		?><a href="<?php Site::out_url('admin'); ?>"><?php _e("Go to comments admin."); ?></a></p><?php
	}
	?>
	<?php if (!$post->info->comments_disabled):
		if($comments->count) echo "<h3>" . _t('Say something!', $theme->name) . "</h3>"; // Comment form header is unnecessary if there are no comments
		?>
		<div id="comment-form">
			<?php if (Session::has_messages()) Session::messages_out(); ?>
			<?php $post->comment_form()->out(); ?>
		</div>
	<?php else: ?>
		<div id="comments-closed">
			<p><?php _e( "Comments are closed for this post.", $theme->name ); ?></p>
		</div>
	<?php endif; ?>
</div>