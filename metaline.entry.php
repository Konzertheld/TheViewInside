<?php
if(count($theme->post->tags))
{
	if($theme->post->pubdate >= $theme->post->updated)
		echo sprintf(_t("An article from %s", $theme->name), $theme->post->pubdate->format());
	else
		echo sprintf(_t("An article from %s, last updated %s", $theme->name), $theme->post->pubdate->format(), $theme->post->updated->format());
	if($theme->post->isguestpost) printf(" - "._t('Guest article by %s', $theme->name), $theme->post->author->displayname);
}	?>