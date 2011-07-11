<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div id="previously">
	<?php if (isset($previously) && sizeof((array)$previously)):?>
	<ul class="postlist">
		<?php foreach ($previously as $prev): ?>
		<li>
			<div class="postlist-meta">
				<h3><a href="<?php echo $prev->permalink; ?>" title="<?php echo $prev->title; ?>"><?php echo $prev->title_out; ?></a></h3>
				<p>Beitrag vom <?php echo $prev->pubdate->out(); ?></p>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>