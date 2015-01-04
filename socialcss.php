<?php if(!defined('HABARI_PATH')) { die('Please do not load this page directly.'); } ?>
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
			echo "background-image:url('" . Site::get_url('theme') . "/images/icons/$image');";
			echo "background-repeat:no-repeat; }";
			echo '#net-' . Utils::slugify($socialnet) . ':hover {';
			echo "background-image:url('" . Site::get_url('theme') . "/images/icons/$revimage'); }";
		}
	}
}
if($opts['social_postfeed'])
{ ?>
#net-postfeed {background-image:url('<?php Site::out_url('theme') ?>/images/icons/postfeed.png'); background-repeat:no-repeat; }
#net-postfeed:hover {background-image:url('<?php Site::out_url('theme') ?>/images/icons/postfeedi.png'); }
<?php }
if($opts['social_commentsfeed'])
{ ?>
#net-commentsfeed {background-image:url('<?php Site::out_url('theme') ?>/images/icons/commentsfeed.png'); background-repeat:no-repeat; }
#net-commentsfeed:hover {background-image:url('<?php Site::out_url('theme') ?>/images/icons/commentsfeedi.png'); }
<?php }
?>
</style>