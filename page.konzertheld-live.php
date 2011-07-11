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
		<div class="post <?php echo Post::type_name($post->content_type); ?> single">
			<?php include("singlenavi.php"); ?>
			<div class="postmeta">
				<h2 class="postmeta-title"><a href="<?php echo $post->permalink; ?>" title="<?php echo $post->title_out; ?>"><?php echo $post->title_out; ?></a></h2>
				<div class="postmeta-meta">
					<?php printf(_t("An article from %s", $theme->name), $post->pubdate->format()); ?>.
					<?php if($guestpost) printf( _t('Guest article by %s', $theme->name), $post->author->displayname); ?>
				</div>
				<?php if (count($post->tags)) : ?>
				<div class="postmeta-tags">
					<?php echo $post->tags_out; ?>
				</div>
				<?php endif; ?>
			</div>
			<div class="postcontent-container">
				<div class="postcontent<?php echo $sideclass; ?>">
					<?php echo $post->content_out;?>
					<?php
					$params["content_type"] = Post::type('Event');
					$params["not:all:info"] = array("ankündigung" => "1");
					$params["status"] = Post::status('published');
					$params["nolimit"] = "";
					$params["has:info"] = "eventdate";
					$params["orderby"] = "cast(hipi1.value as unsigned) DESC";
					$events = Posts::get($params);

					// Now group them by eventtag
					foreach($events as $event) $eventsgrouped[$event->info->eventtag][] = $event;
					?>
					<div class="eventlist">
						<ul>
							<?php foreach ($eventsgrouped as $eventgroupname => $eventgroup): ?>
							<li class="clearfix">
								<h4><a href="/eventtag/<?php echo $eventgroupname?>"><?php echo $eventgroupname;?></a></h4>
								<ul>
									<?php foreach ($eventgroup as $event): ?>
									<li>
										<?php echo $event->info->eventdate_out;?> @ <a href="/location/<?php echo $event->info->location;?>"><?php echo $event->info->location;?></a> - <a href="<?php echo $event->permalink; ?>" title="<?php echo $event->title; ?>"><?php echo $event->title_out; ?></a>
									</li>
									<?php endforeach; ?>
								</ul>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php echo $sidecontentstring; ?>
			</div>
			<?php include("comments.php"); ?>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>