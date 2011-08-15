<?php
if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); }
// DO NOT RENAME THIS FILE. Files named content.php cause conflicts with template fallbacks.
?>
<?php
$theme->evenodd = true;
foreach ( $posts as $post )
{
	$theme->content($post, 'multiple');
	$theme->evenodd = !$theme->evenodd;
}
?>
<div id="page-selector">
	<?php $theme->prev_page_link( _t('&laquo; Newer', $theme->name) ); ?> <?php $theme->page_selector( null, array( 'leftSide' => 4, 'rightSide' => 4 ) ); ?> <?php $theme->next_page_link( _t( 'Older &raquo;', $theme->name ) ); ?>
</div>