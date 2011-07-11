<?php
// Change "updated" to "modified" when you want minor edits included
echo sprintf(_t("Information page, last updated %s"), $this->post->updated->format());
?>