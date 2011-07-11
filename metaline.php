<?php
// TODO: Localise this correctly using classes
// TODO: Put the strings in the theme options, there should be nothing hardcoded here

{
	case "page":
		// Change "updated" to "modified" when you want minor edits included
		return sprintf(_t("Information page, last updated %s"), $post->updated->format());
	case "event":
		// TODO: Should we really add the time, when no date is provided?
		$metastring = "";
		if(isset($post->info->eventtag) && !empty($post->info->eventtag)) $metastring .= $post->info->eventtag_out;
		else $metastring .= _t("an event");
		if(isset($post->info->eventdate) && !empty($post->info->eventdate))
		{
			if(!empty($metastring)) $metastring .= ", ";
			$metastring .= $post->info->eventdate_out;
		}
		if(isset($post->info->eventtime) && !empty($post->info->eventtime))
		{
			if(!empty($metastring)) $metastring .= ", ";
			$metastring .= $post->info->eventtime_out;
		}
		echo $metastring;
	case "entry":
	default:
		echo sprintf(_t("An article from %s"), $post->pubdate->format());
}
?>