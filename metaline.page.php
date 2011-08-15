<?php
// Change "updated" to "modified" when you want minor edits included
echo sprintf(_t("Information page, last updated %s", $theme->name), $theme->post->updated->format());
if($theme->post->isguestpost) printf(" - "._t('Guest article by %s', $theme->name), $theme->post->author->displayname); ?>