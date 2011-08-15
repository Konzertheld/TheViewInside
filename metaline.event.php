<?php
// TODO: Put the strings in the theme options, there should be nothing hardcoded here
// TODO: Should we really add the time, when no date is provided?
_e("A report about ", $theme->name);

if(isset($theme->post->info->eventtag) && !empty($theme->post->info->eventtag)) echo $theme->post->info->eventtag_out;
else _e("an event", $theme->name);
echo ". ";
if(isset($theme->post->info->location) && !empty($theme->post->info->location))
	echo $theme->post->info->location_out;
if(isset($theme->post->info->eventdate) && !empty($theme->post->info->eventdate))
{
	if(isset($theme->post->info->location) && !empty($theme->post->info->location))
		echo ", ";
	echo $theme->post->info->eventdate_out;
	if(isset($theme->post->info->eventtime) && !empty($theme->post->info->eventtime))
	{
		echo ", ";
		echo $theme->post->info->eventtime_out;
	}
}
?>
<?php if($theme->post->isguestpost) printf("<br>"._t('Guest article by %s', $theme->name), $theme->post->author->displayname); ?>