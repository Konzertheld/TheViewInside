<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div class="multiple post <?php echo Post::type_name($content->content_type[0]); ?><?php if($theme->evenodd) echo ' even'; else echo ' odd';?>">
	<div class="postmeta">
		<?php include("multinavi.php"); ?>
		<h2 class="postmeta-title"><a href="<?php echo $content->permalink; ?>" title="<?php echo $content->title_out; ?>"><?php echo $content->title_out; ?></a></h2>
		<div class="postmeta-meta">
			<a href="<?php echo $content->info->url; ?>"><?php echo $content->info->url; ?></a>
		</div>
	</div>
	<div class="postcontent-container">
		<div class="postcontent">
		<?php echo $content->content_out;?>
		<?php if(count($content->tags)):?>
		<div class="postmeta-tags">
		<span>Bloggt &uuml;ber:</span> <?php echo $content->tags_out; ?>
		</div><?php endif;?>
		</div>
	</div>
</div>