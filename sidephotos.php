<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); }
foreach($content->tvi_photos as $photo)
{
	if($photo instanceof MediaAsset) {
		?><a href="<?php echo Site::get_url('site'); ?>gallery/<?php echo $content->slug; ?>?start=<?php echo $photo->path; ?>"><img src="<?php echo $photo->thumbnail; ?>" alt="<?php echo $photo->title; ?>"></a><?php
	}
	else {
		echo $photo;
	}
}
?>