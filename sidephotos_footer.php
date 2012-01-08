<?php
if(count($content->tvi_photosources))
{
	_e("Sources: ", $theme->name);
	foreach($content->tvi_photosources as $i => $source)
		echo "<a href='$source'>[". ($i+1) ."]</a> ";
}
// TODO: Make this optional. Private albums are meant to be private.
$picasaalbum = $content->info->picasa_album;
if(!empty($picasaalbum))
{ ?><p class="sidecontent-footer"><?php
	_e("View entire album: ", $theme->name);
	echo "<a href='".$content->picasalink."'>".$content->info->picasa_album."</a>";
	?></p><?php
}
?>