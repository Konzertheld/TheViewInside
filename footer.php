<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
	<div id="footer">
		<?php $theme->area('footer'); ?>
		<?php if(($multipleview && $gpmultiple) || (!$multipleview && $gpsingle)):?><script type="text/javascript">gapi.plusone.go();</script><?php endif; ?>
		<?php $theme->footer(); ?>
	</div>
</div>