<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php error_reporting(1); ?>
<!DOCTYPE HTML>
<head>
	<title><?php if(!$theme->multipleview) { echo $post->title." | "; Options::out( 'title' ); } else { Options::out( 'title' ); echo ' | '; Options::out( 'tagline' ); } ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="generator" content="Habari <?php echo Version::get_habariversion() ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php $theme->feed_alternate(); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/color.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/debug.css" media="screen" />
	<?php if($showgoogleplus): ?><script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script><?php endif; ?>
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/ie.css" media="screen" />
	<![endif]-->
	<?php $theme->header(); ?>
</head>
<body>
<div id="wrapper">
	<div id="headerbarcontainer">
		<div id="headerbar">
			<?php $theme->area('headerbar'); ?>
		</div>
		<a href="/" id="homelogo" title="Startseite">Konzertheld.de</a>
		<div id="socialcontainer">
			<a href="http://www.lastfm.de/user/konzertheld" id="net-lastfm" title="last.fm" class="socialneticon"></a>
			<a href="http://picasaweb.google.de/Konzertheld" id="net-picasa" title="Picasa" class="socialneticon"></a>
			<a href="http://www.youtube.com/user/konzertheld" id="net-youtube" title="YouTube" class="socialneticon"></a>
			<a href="http://www.flickr.com/photos/konzertheld/" id="net-flickr" title="Flickr Fotostream" class="socialneticon"></a>
			<a href="http://konzertheld.deviantart.com/" id="net-deviantart" title="Deviantart-Profil" class="socialneticon"></a>
			<a href="http://www.bookcrossing.com/mybookshelf/Konzertheld/" id="net-bookcrossing" title="Bookcrossing Bücherregal" class="socialneticon"></a>
			<a href="http://konzertheld.de/atom/1" id="net-rss" title="Atom-Feed Posts" class="socialneticon"></a>
		</div>
	</div>