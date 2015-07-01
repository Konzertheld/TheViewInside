<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php if($post->tvi_hasphotos) $tvi_photos_out = $post->tvi_photos_out; ?>
<?php $theme->display('header'); ?>
<div id="contentcontainer">
	<div id="content">
		<div class="single post <?php echo Post::type_name($post->content_type[0]); ?><?php if($theme->evenodd) echo ' even'; else echo ' odd';?>">
			<div class="postmeta">
				<h2 class="postmeta-title"><a href="<?php echo $post->permalink; ?>" title="<?php echo $post->title_out; ?>"><?php echo $post->title_out; ?></a></h2>
				<?php include("singlenavi.php"); ?>
				<div class="postmeta-meta">
					<?php echo $theme->metaline($post); ?>
				</div>
				<?php if (count($post->tags)) : ?>
				<div class="postmeta-tags">
					<?php echo $post->tags_out; ?>
				</div>
				<?php endif; ?>
			</div>
			<div <?php if($post->tvi_hasphotos && isset($post->info->max_images)): ?>style="min-height: <?php echo $theme->sidephoto_height; ?>px"<?php endif; ?> class="postcontent-container">
				<div class="postcontent<?php if($post->tvi_hasphotos) echo " withsidecontent"; ?>">
					<?php echo $post->content_out;?>
				</div>
				<?php if($post->tvi_hasphotos): ?>
				<div class='postside'>
					<div class='postside-photos'>
						<?php echo $tvi_photos_out; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php include("comments.php"); ?>
	</div>
</div>
<?php $theme->display('footer'); ?>