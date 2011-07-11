<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php
	// hardcoded crap to determine if the post's author is a guest
	// this should be done at least with a theme setting
	// and it should be made optional
	$guestpost = ($post->author->displayname!="Konzertheld");
?>
<?php
	// Load the sidephotos, if any, and prepare variables used later
	// This is required in every template that uses sidephotos
	// Before, set $content = $post, if necessary (the post is in $content in multiple views, but in $post in single views)
	if(!isset($content)) $content = $post;
	include("sidephotos.php");
?>
<?php include 'header.php'; ?>
<div id="contentcontainer">
	<div id="content">
		<div class="post entry single">
			<?php include("navi.single.php"); ?>
			<div class="postmeta">
				<h2 class="postmeta-title"><a href="<?php echo $post->permalink; ?>" title="<?php echo $post->title_out; ?>"><?php echo $post->title_out; ?></a></h2>
				<div class="postmeta-meta">
					<?php printf(_t("An article from %s"), $post->pubdate->format()); ?>.
					<?php if($guestpost) printf( _t('Guest article by %s'), $post->author->displayname); ?>
				</div>
				<?php if (count($post->tags)) : ?>
				<div class="postmeta-tags">
					<?php echo $post->tags_out; ?>
				</div>
				<?php endif; ?>
			</div>
			<div class="postcontent-container">
				<div class="postcontent<?php echo $sideclass; ?>">
					<?php $theme->picasaboom("Rock the Church 2011", "s100"); ?>
				</div>
				<?php echo $sidecontentstring; ?>
			</div>
			<?php include("comments.php"); ?>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>