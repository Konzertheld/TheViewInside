<?php
$dpost = $post->descend();
$apost = $post->ascend();
?>

<p class="pagination">
<?php if(is_object($dpost)): ?>
	<a href="<?php echo $dpost->permalink; ?>">
	<img src="<?php Site::out_url('theme'); ?>/images/icons/arrow_left.png" alt="<?php _e('Previous Post', $theme->name); ?>" title="<?php _e('Previous Post', $theme->name); ?>" class="paginationicon">
	</a>
<?php else: ?>
	<img src="<?php Site::out_url('theme'); ?>/images/icons/arrow_left_g.png" alt="<?php _e('There is no previous Post', $theme->name); ?>" title="<?php _e('There is no previous Post', $theme->name); ?>" class="paginationicon">
<?php endif; ?>
<a href="<?php Site::out_url('habari'); ?>">
<img src="<?php Site::out_url('theme'); ?>/images/icons/arrow_up.png" alt="<?php _e('Back to homepage', $theme->name); ?>" title="<?php _e('Back to homepage', $theme->name); ?>" class="paginationicon">
</a>
<?php if ($user instanceof User && $loggedin): ?>
	<a href="<?php Site::out_url('admin');?>/publish?id=<?php echo $post->id; ?>">
	<img src="<?php Site::out_url('theme'); ?>/images/icons/page_edit.png" alt="<?php _e('Edit post', $theme->name); ?>" title="<?php _e('Edit post', $theme->name); ?>" class="paginationicon">
	</a>
<?php endif;?>
<a href="<?php echo $post->permalink;?>/atom/comments">
<img src="<?php Site::out_url('theme'); ?>/images/icons/feed.png" alt="<?php _e('Subscribe to this post\'s comments', $theme->name); ?>" title="<?php _e('Subscribe to this post\'s comments', $theme->name); ?>" class="paginationicon">
</a>
<?php if(is_object($apost)): ?>
	<a href="<?php echo $apost->permalink; ?>">
	<img src="<?php Site::out_url('theme'); ?>/images/icons/arrow_right.png" alt="<?php _e('Next Post', $theme->name); ?>" title="<?php _e('Next Post', $theme->name); ?>" class="paginationicon">
	</a>
<?php else: ?>
	<img src="<?php Site::out_url('theme'); ?>/images/icons/arrow_right_g.png" alt="<?php _e('There is no next Post', $theme->name); ?>" title="<?php _e('There is no next Post', $theme->name); ?>" class="paginationicon">
<?php endif; ?></p>