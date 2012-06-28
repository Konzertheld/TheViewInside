<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<?php error_reporting(1); ?>
<!DOCTYPE HTML>
<head>
	<title><?php if(!$multipleview) { echo $post->title." | "; Options::out( 'title' ); } else { Options::out( 'title' ); echo ' | '; Options::out( 'tagline' ); } ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="generator" content="Habari <?php echo Version::get_habariversion() ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php $theme->feed_alternate(); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/color.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/debug.css" media="screen" />
	<?php require_once('socialcss.php'); ?>
	<?php if(($multipleview && $gpmultiple) || (!$multipleview && $gpsingle)): ?><script type="text/javascript" src="https://apis.google.com/js/plusone.js">{parsetags: 'explicit'}</script><?php endif; ?>
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php Site::out_url( 'theme' ); ?>/ie.css" media="screen" />
	<![endif]-->
	<?php echo $theme->header(); ?>
</head>
<body>
<div id="wrapper">
	<div id="headerbarcontainer">
		<div id="headerbar">
			<?php echo $theme->area('headerbar'); ?>
		</div>
		<h1><a href="/" id="homelogo" title="Startseite"><?php Options::out( 'title' ); ?></a></h1>
		<div id="socialcontainer">
			<?php echo $theme->socialneticons(); ?>
		</div>
	</div>