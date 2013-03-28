<?php namespace Habari;
if(count($content->tags)): ?>
<p class="postnavi">
<a href="<?php echo $content->permalink; ?>#comments">
<img src="<?php Site::out_url('theme'); ?>/images/icons/comments.png" alt="<?php _e('Jump to comments', $theme->name); ?>" title="<?php _e('Jump to comments', $theme->name); ?>" class="paginationicon">
</a>
<?php if ($user instanceof User && $loggedin): ?>
	<a href="<?php Site::out_url('admin');?>/publish?id=<?php echo $content->id; ?>">
	<img src="<?php Site::out_url('theme'); ?>/images/icons/page_edit.png" alt="<?php _e('Edit post', $theme->name); ?>" title="<?php _e('Edit post', $theme->name); ?>" class="paginationicon">
	</a>
<?php endif;?>
<a href="#">
<img src="<?php Site::out_url('theme'); ?>/images/icons/arrow_up.png" alt="<?php _e('Jump to top', $theme->name); ?>" title="<?php _e('Jump to top', $theme->name); ?>" class="paginationicon">
</a>
<?php echo $theme->jumplist($content, true); ?>
</p>
<?php endif; ?>