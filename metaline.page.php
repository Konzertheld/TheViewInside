<?php
// Change "updated" to "modified" when you want minor edits included
_e("Information page, last updated %s", array($theme->post->updated->format()), $theme->name);
if($theme->post->isguestpost) printf(" - "._t('Guest article by %s', $theme->name), $theme->post->author->displayname); ?>