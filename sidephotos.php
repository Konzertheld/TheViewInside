<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php
$i = 0;
foreach($content->tvi_photos as $photo)
{
	echo $photo;
	$i++;
	if(is_numeric($content->info->max_images) && $i >= $content->info->max_images) break;
}
?>