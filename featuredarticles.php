<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div id="featuredarticles">
	<?php if (isset($featuredarticles) && count(featuredarticles)):?>
	<ul class="postlist">
		<?php foreach ($featuredarticles as $article): ?>
		<li>
			<div class="postlist-meta">
				<h3><a href="<?php echo $article->permalink; ?>" title="<?php echo $article->title; ?>"><?php echo $article->title_out; ?></a></h3>
				<p>Beitrag vom <?php echo $article->pubdate->out(); ?></p>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>