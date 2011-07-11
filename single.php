<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php include 'header.php'; ?>
<div id="contentcontainer">
	<div id="content">
		<div class="multiple post <?php echo Post::type_name($post->content_type[0]); ?><?php if($theme->evenodd) echo ' even'; else echo ' odd';?>">
			<div class="postmeta">
				<h2 class="postmeta-title"><a href="<?php echo $post->permalink; ?>" title="<?php echo $post->title_out; ?>"><?php echo $post->title_out; ?></a></h2>
				<div class="postmeta-meta">
					<?php $theme->metaline($post); ?>
					<?php if($post->isguestpost) printf( _t('Guest article by %s', $theme->name), $post->author->displayname); ?>
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
					<?php if (User::identify()->id): ?>
					<p class="editpostlink"><a href="<?php Site::out_url('admin');?>/publish?id=<?php echo $post->id; ?>" title="<?php printf(_t('Edit %s', $theme->name), $post->title); ?>"><?php printf(_t('Edit %s', $theme->name), $post->title); ?></a></p>
					<?php endif; ?>
				</div>
				<?php if(count($post->tvi_photos)): ?>
				<div class='postside'>
					<div class='postside-photos'>
						<?php echo $post->tvi_photos_out; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php include("comments.php"); ?>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>