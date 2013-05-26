<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php include 'header.php'; ?>
<div id="contentcontainer">
	<div id="content">
		<div class="single post <?php echo Post::type_name($post->content_type[0]); ?><?php if($theme->evenodd) echo ' even'; else echo ' odd';?>">
			<?php include("singlenavi.php"); ?>
			<div class="postmeta">
				<h2 class="postmeta-title"><a href="<?php echo $post->permalink; ?>" title="<?php echo $post->title_out; ?>"><?php echo $post->title_out; ?></a></h2>
				<div class="postmeta-meta">
					<?php echo $theme->metaline($post); ?>
				</div>
				<?php if (count($post->tags)) : ?>
				<div class="postmeta-tags">
					<?php echo $post->tags_out; ?>
				</div>
				<?php endif; ?>
			</div>
			<div class="postcontent-container">
				<div class="postcontent<?php if(count($post->tvi_photos)) echo " withsidecontent"; ?>">
					<?php echo $post->content_out;?>
				</div>
				<?php if(count($post->tvi_photos)): ?>
				<div class='postside'>
					<div class='postside-photos'>
						<?php echo $post->tvi_photos_out; ?>
					</div>
					<div class='postside-photos-footer'>
						<?php //TODO: Make this optional to protect private albums
						echo $post->tvi_photos_footer_out; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php include("comments.php"); ?>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>