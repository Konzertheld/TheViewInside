<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div class="multiple post <?php echo Post::type_name($content->content_type[0]); ?><?php if($theme->evenodd) echo ' even'; else echo ' odd';?>">
	<div class="postmeta">
		<h2 class="postmeta-title"><a href="<?php echo $content->permalink; ?>" title="<?php echo $content->title_out; ?>"><?php echo $content->title_out; ?></a></h2>
		<div class="postmeta-meta">
			<?php $theme->metaline($content); ?>
		</div>
		<?php if (count($content->tags)) : ?>
		<div class="postmeta-tags">
			<?php echo $content->tags_out; ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="postcontent-container">
		<div class="postcontent<?php if(count($content->tvi_photos)) echo " withsidecontent"; ?>">
			<?php echo $content->content_out;?>
			<?php if (User::identify()->id): ?>
			<p class="editpostlink"><a href="<?php Site::out_url('admin');?>/publish?id=<?php echo $content->id; ?>" title="<?php printf(_t('Edit %s', $theme->name), $content->title); ?>"><?php _e('Edit post', $theme->name); ?></a></p>
			<?php endif; ?>
		</div>
		<?php if(count($content->tvi_photos)): ?>
		<div class='postside'>
			<div class='postside-photos'>
				<?php echo $content->tvi_photos_out; ?>
				<?php //$theme->area('sidephotos'); ?>
			</div>
		</div>
		
		<?php endif; ?>
	</div>
</div>