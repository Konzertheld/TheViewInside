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
if(count($content->tvi_photosources))
{
	_e("Sources: ", $theme->name);
	foreach($content->tvi_photosources as $i => $source)
		echo "<a href='$source'>[". ($i+1) ."]</a> ";
}
// TODO: Make this optional. Private albums are meant to be private.
$picasaalbum = $content->tvi_picasaalbum;
if(!empty($picasaalbum))
{ ?><p class="sidecontent-footer"><?php
	_e("View entire album: ", $theme->name);
	echo "<a href='$content->tvi_picasaalbum_out'>$content->tvi_picasaalbum</a>";
	?></p><?php
}
?>