<?php
	// Create queries for ascending and descending posts
	// Content Types from options
	$query = "";
	foreach($content_types as $typenr)
	{
		$query .= "content_type=$typenr OR ";
	}
	$query = substr($query, 0, strlen($query)-4);
	$query = " AND status={$post->status} AND ($query)";
	$dparams = array('where' => "pubdate <= '{$post->pubdate->sql}' $query");
	$aparams = array('where' => "pubdate >= '{$post->pubdate->sql}' $query");
?>

<p class="pagination"><?php if(is_object($post->descend($dparams))):?><a href="<?php echo $post->descend($dparams)->permalink; ?>">&lt; Vorheriger Post</a> | <?php endif; ?>
<?php if ($user instanceof User && $loggedin): ?><a href="<?php Site::out_url('admin');?>/publish?id=<?php echo $post->id; ?>" title="<?php printf(_t('Edit %s'), $post->title); ?>"><?php _e('Edit post', $theme->name); ?></a><?php endif;?><? if ($user instanceof User && $loggedin)echo ' | ';?>
<?php if(is_object($post->ascend($aparams))):?> <a href="<?php echo $post->ascend($aparams)->permalink; ?>">N&auml;chster Post &gt;</a><?php endif; ?></p>