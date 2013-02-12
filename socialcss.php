<?php namespace Habari; if(!defined('HABARI_PATH')) { die('Please do not load this page directly.'); } ?>
<style type="text/css">
<?php
$opts = Options::get_group(get_class($theme));
if(isset($opts['socialnets']))
{
	$socialnetslist = explode(',', $opts['socialnets']);
	foreach($socialnetslist as $socialnet)
	{	
		$image = $opts[Utils::slugify($socialnet) . '__img'];
		if(!empty($image))
		{
			$revimage = substr($image, 0, strrpos($image, '.')) . 'i' . substr($image, strrpos($image, '.'));
			echo '#net-' . Utils::slugify($socialnet) . ' {';
			echo "background:transparent url('" . Site::get_url('theme') . "/images/icons/$image') no-repeat; }";
			echo '#net-' . Utils::slugify($socialnet) . ':hover {';
			echo "background:transparent url('" . Site::get_url('theme') . "/images/icons/$revimage') no-repeat; }";
		}
	}
}
if($opts['social_postfeed'])
{ ?>
#net-postfeed {background:transparent url('<?php Site::out_url('theme') ?>/images/icons/postfeed.png') no-repeat; }
#net-postfeed:hover {background:transparent url('<?php Site::out_url('theme') ?>/images/icons/postfeedi.png') no-repeat; }
<?php }
if($opts['social_commentsfeed'])
{ ?>
#net-commentsfeed {background:transparent url('<?php Site::out_url('theme') ?>/images/icons/commentsfeed.png') no-repeat; }
#net-commentsfeed:hover {background:transparent url('<?php Site::out_url('theme') ?>/images/icons/commentsfeedi.png') no-repeat; }
<?php }
?>
</style>