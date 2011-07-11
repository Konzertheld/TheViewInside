<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div id="comments">
	<h3 id="comments-headline">
		<?php if(count($post->comments->moderated))
		{
			printf( _n('%1$d comment so far', '%1$d comments so far', $post->comments->moderated->count), $post->comments->moderated->count );?>. <?php
		}
		else _e('No comments yet. Start the discussion!'); ?>
	</h3>
	<?php if ( $post->comments->moderated->count ): ?>
		<?php $evenoddcomment = true;
		foreach ( $post->comments->moderated as $comment ): ?>
			<div id="comment-<?php echo $comment->id; ?>" class="comment<?php if($evenoddcomment) echo ' even'; else echo ' odd';?>">
				<?php if (Plugins::is_loaded('Gravatar')): ?>
				<div class="gravatar"><img src="<?php echo $comment->gravatar ?>" alt="<?php printf(_t("%s's Gravatar"), $comment->name); ?>"></div>
				<?php endif; ?>
				<div class="comment-main">
					<div class="comment-meta">
						<?php printf(_t(' %s said at %s:'),
						'<span class="comment-author">'.$theme->theme_comment_author_link($theme, $comment).'</span>',
						'<span><a href="'.$post->permalink.'#comment-'.$comment->id.'">'.$comment->date->format().'</a></span>'); ?>
					</div>
					<div class="comment-content">
						<?php echo $comment->content_out; ?>
						<?php if ( $comment->status == Comment::STATUS_UNAPPROVED ) : ?>
						<p class="newunapproved"><em><?php _e( 'Your comment is awaiting moderation' ) ;?></em></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php $evenoddcomment = !$evenoddcomment;
		endforeach; ?>
	<?php endif; ?>
	<?php if ( !$post->info->comments_disabled ) : ?>
		<?php if(count($post->comments->moderated)): ?><h3><?php _e('Say something!'); ?></h3><?php endif; ?>
		<div id="comment-form">
			<?php 	if ( Session::has_messages() ) Session::messages_out(); ?>
			<?php 	$post->comment_form()->out(); ?>
			<?php if ( Plugins::is_loaded( 'Captcha' ) ): $theme->show_captcha(); endif; ?>
			Du kannst Kommentare zu diesem Post <a href="<?php echo $post->permalink;?>/atom/comments">hier abonnieren</a>. Oder du abonnierst den <a href="<?php URL::out( 'atom_feed_comments' ); ?>">globalen Kommentarfeed</a>.
		</div>
	<?php else: ?> 
		<div id="comments-closed">
				<p><?php _e( "Comments are closed for this post" ); ?></p>
		</div>
	<?php endif; ?>

</div>