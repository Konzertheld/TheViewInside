<?php
// TODO: Localise this correctly using classes
// TODO: Put the strings in the theme options, there should be nothing hardcoded here
// TODO: Should we really add the time, when no date is provided?
_e("A report about ", $theme->name);

if(isset($this->post->info->eventtag) && !empty($this->post->info->eventtag)) echo $this->post->info->eventtag_out;
else _e("an event", $theme->name);
echo ". ";
if(isset($this->post->info->location) && !empty($this->post->info->location))
	echo $this->post->info->location_out;
if(isset($this->post->info->eventdate) && !empty($this->post->info->eventdate))
{
	if(isset($this->post->info->location) && !empty($this->post->info->location))
		echo ", ";
	echo $this->post->info->eventdate_out;
	if(isset($this->post->info->eventtime) && !empty($this->post->info->eventtime))
	{
		echo ", ";
		echo $this->post->info->eventtime_out;
	}
}
?>