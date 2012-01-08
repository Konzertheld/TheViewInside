<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<h2><?php _e($content->title); ?></h2>
<?php if(count($content->pages)): ?>
<ul class="postlist">
	<?php foreach($content->pages as $page): ?>
		<li>
			<div class="postlist-meta">
				<h3><a href="<?php echo $page->permalink; ?>" title="<?php echo $page->title; ?>"><?php echo $page->title_out; ?></a></h3>
				<p><?php $description = $page->info->viewinsidedescription;
				if(empty($description))
					_e("Information page, last updated %s", array($theme->post->updated->format()), $theme->name);
				else
					echo $description;
				?></p>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
<?php else: _e('Nothing to see here.');?>
<?php endif; ?>