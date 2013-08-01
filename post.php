<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div class="multiple post <?php echo Post::type_name($content->content_type[0]); ?>
<?php if($theme->evenodd) echo ' even'; else echo ' odd';?>
<?php if(!count($content->tags)) echo ' notags';?>">
	<div class="postmeta">
		<?php include("multinavi.php"); ?>
		<h2 class="postmeta-title"><a href="<?php echo $content->permalink; ?>" title="<?php echo $content->title_out; ?>"><?php echo $content->title_out; ?></a></h2>
		<div class="postmeta-meta">
			<?php echo $theme->metaline($content); ?>
		</div>
		<?php if (count($content->tags)) : ?>
		<div class="postmeta-tags">
			<?php echo $content->tags_out; ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="postcontent-container">
		<div class="postcontent<?php if($content->tvi_hasphotos) echo " withsidecontent"; ?>">
			<?php echo $content->content_out;?>
		</div>
		<?php if($content->tvi_hasphotos): ?>
		<div class='postside'>
			<div class='postside-photos'>
				<?php echo $content->tvi_photos_out; ?>
				<?php //$theme->area('sidephotos'); ?>
			</div>
		</div>
		
		<?php endif; ?>
	</div>
</div>