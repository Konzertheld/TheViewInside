<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php
$i = 0;
foreach($content->tvi_photos as $photo)
{
	if(is_array($photo) && isset($photo['picasa_url']))
	{ // Picasa image or similar
		?><a href="<?php echo $photo['picasa_url']; ?>"><img src="<?php echo $photo['url']; ?>"></a><?php
	}
	else if(is_array($photo) && isset($photo["out"]))
	{ // Array with information from extracted image
		echo $photo["out"];
	}
	else if(is_file($photo) || substr($photo, 0, 4) == "http")
	{ // Single file without image tag
		echo "<img src='$photo' alt='sidephoto'>";
	}
	else echo $photo;
	$i++;
	if(is_numeric($content->info->max_images) && $i >= $content->info->max_images) break;
}
?>