<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
	<div id="footer">
		<?php echo $theme->area('footer'); ?>
		<?php if(($multipleview && $gpmultiple) || (!$multipleview && $gpsingle)):?><script type="text/javascript">gapi.plusone.go();</script><?php endif; ?>
		<?php echo $theme->footer(); ?>
	</div>
</div>