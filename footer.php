<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
	<div id="footer">
		<div id="tagcloud" class="tag-cloud"><h2><?php _e("Common topics"); ?></h2><?php $theme->tag_cloud(150); ?></div>
	</div>
</div>
<?php 
// -- Piwik Tracking API init -- 

require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://konzertheld.de/piwik/';

$t = new PiwikTracker( $idSite = 1, 'http://konzertheld.de/piwik/');
// Optional function calls
$t->setResolution(1337, 42 );

// set a Custom Variable called 'Gender'
$t->setCustomVariable( 1, 'theme', $theme->name );
if ($user instanceof User && $loggedin)
	$t->setCustomVariable(2, 'user', $user->displayname);

// Mandatory: set the URL being tracked
$t->setUrl( $url = $_SERVER['REQUEST_URI'] );

// Finally, track the page view with a Custom Page Title
// In the standard JS API, the content of the <title> tag would be set as the page title
$t->doTrackPageView(Options::get('title'));
?>