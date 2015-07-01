<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php if($content->tvi_hasphotos) $tvi_photos_out = $content->tvi_photos_out; ?>
<div class="multiple post <?php echo Post::type_name($content->content_type[0]); ?>
<?php if($theme->evenodd) echo ' even'; else echo ' odd';?>
<?php if(!count($content->tags)) echo ' notags';?>">
	<div class="postmeta">
		<h2 class="postmeta-title"><a href="<?php echo $content->permalink; ?>" title="<?php echo $content->title_out; ?>"><?php echo $content->title_out; ?></a></h2>
		<?php include("multinavi.php"); ?>
		<div class="postmeta-meta">
			<?php echo $theme->metaline($content); ?>
		</div>
		<?php if (count($content->tags)) : ?>
		<div class="postmeta-tags">
			<?php echo $content->tags_out; ?>
		</div>
		<?php endif; ?>
	</div>
	<div <?php if($content->tvi_hasphotos && isset($content->info->max_images)): ?>style="min-height: <?php echo $theme->sidephoto_height; ?>px"<?php endif; ?> class="postcontent-container">
		<div class="postcontent<?php if($content->tvi_hasphotos) echo " withsidecontent"; ?>">
			<?php echo $content->content_out;?>
		</div>
		<?php if($content->tvi_hasphotos): ?>
		<div class='postside'>
			<div class='postside-photos'>
				<?php echo $tvi_photos_out; ?>
			</div>
		</div>
		
		<?php endif; ?>
	</div>
</div>