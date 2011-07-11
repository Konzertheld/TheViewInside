<?php
/**
 * TheViewInside Habari Theme
 * by Konzertheld
 * http://konzertheld.de
 */

class TheViewInside extends Theme
{
	/**
	 * Number of previously items to display.
	 */
	const PREVIOUSLY_ITEMS = 3;

	/**
	 * Number of latest items to display.
	 */
	const LATEST_ITEMS = 3;
	
	var $defaultsettings = array(
		'content_types' => 'entry',
		);
		
	var $image_extensions = array('.png', '.jpg');
	
	var $excludedclasses = array("rating");
	//$excludedclasses[] = "blockintextlinks";
	//$excludedclasses[] = "blockintextrechts";
	
	public function action_theme_activated()
	{
		$opts = Options::get_group( __CLASS__ );
		if ( empty( $opts ) ) {
			Options::set_group( __CLASS__, $this->defaults );
		}
	}
		
	/**
	 * Execute on theme init to apply these filters to output
	 */
	public function action_init_theme()
	{
		// Apply Format::autop() to post content
		//Format::apply( 'autop', 'post_content_out' );

		// Apply Format::autop() to comment content
		Format::apply( 'autop', 'comment_content_out' );
		
		$this->load_text_domain( 'TheViewInside' );
	}
	
	/**
	 * Create theme options
	 */
	public function action_theme_ui( $theme )
	{
		$ui = new FormUI( __CLASS__ );
		// Get the available content types
		$types = Post::list_active_post_types();
		unset($types['any']);
		$types = array_flip($types);
		// foreach($types as $id => $type)
		// {
			// pointless, because singular/plural cannot be get here
			// $types[$id] = Post::type_name($id);
		// }
		$ui->append( 'select', 'content_types', __CLASS__.'__content_types', _t( 'Content Types in pagination:', $theme->name ) );
		$ui->content_types->size = count($types);
		$ui->content_types->multiple = true;		
		$ui->content_types->options = $types;
		$ui->append ( 'text', 'blatest', __CLASS__.'blatest', 'useless');
		$ui->blatest->maxlength = 3;

		// Save
		$ui->append( 'submit', 'save', _t( 'Save', $theme->name ) );
		$ui->set_option( 'success_message', _t( 'Options saved', $theme->name ) );
		$ui->out();
	}

	/**
	 * Add some variables to the template output
	 */
	public function action_add_template_vars()
	{
		if( !$this->template_engine->assigned( 'pages' ) ) {
			$this->assign('pages', Posts::get( array( 'content_type' => 'page', 'status' => Post::status('published'), 'nolimit' => 1 ) ) );
		}

		$page = Controller::get_var( 'page' );
		$page = isset ( $page ) ? $page : 1;
		if( !$this->template_engine->assigned( 'page' ) ) {
			$this->assign('page', $page );
		}

		$this->assign( 'show_previously', false );
		$this->assign( 'show_latest', false );
		$this->assign( 'multipleview', false);
		$action = Controller::get_action();
		if ($action == 'display_home' || $action == 'display_entries' || $action == 'search' || $action == 'display_tag' || $action == 'display_date') {
			$this->assign('multipleview', true);
			// Was tut das Zeug im if-block unter diesem Kommentar?
			$offset = (int) ( ( ( $page + 1 ) - 1 ) * Options::get('pagination') );
			$this->assign( 'previously', Posts::get(array ( 'status' => 'published', 'offset' => $offset, 'limit' => self::PREVIOUSLY_ITEMS ) ) );
			$this->assign( 'show_previously', true );
		}

		if ($action != 'display_home') {
			$this->assign( 'previously', Posts::get(array ( 'status' => 'published', 'offset' => 0, 'limit' => self::LATEST_ITEMS ) ) );
			$this->assign( 'show_previously', true );
		}

		$this->assign('controller_action', $action);
		
		// Use theme options to set values that can be used directly in the templates
		$opts = Options::get_group( __CLASS__ );
		$this->assign('content_types', $opts['content_types']);
	}

	/**
	 * Convert a post's tags ArrayObject into a usable list of links
	 *
	 * @param array $array The Tags object from a Post object
	 * @return string The HTML of the linked tags
	 */
	public function filter_post_tags_out($array)
	{
		foreach($array as $tag) $array_out[] = "<li><a href='" . URL::get("display_entries_by_tag", array( "tag" => $tag->term) ) . "' rel='tag'>" . $tag->term_display . "</a></li>\n";
		$out = '<ul>' . implode('', $array_out) . '</ul>';
		return $out;
 	}
	
	
	public function filter_post_content_out($content, $post)
	{
		// Trim post content where the more-tag is used when using content_out
		$newcontent = $content;
		if($this->multipleview)
		{
			$morepos = stripos($content, "<!--more-->");
			if(!$morepos) $morepos = stripos($content, "<!-- more -->");
			if($morepos != false)
				$newcontent = substr($content, 0, $morepos)."<p><a class='readmorelink' href='".$post->permalink."' title='".sprintf(_t("Continue reading %s", $theme->name),$post->title)."'>".sprintf(_t("Continue reading %s", $theme->name),$post->title)."</a></p>";
		}
		
		// Remove images
		if($post->info->remove_images)
		{
			// Get images
			$images = $this->post_get_images($newcontent);
			foreach($images as $image)
				$newcontent = str_replace($image["original"], "", $newcontent);
		}
		return $newcontent;
	}

	// TODO Hier müsste man auch mal was gescheites machen
	// Was tut diese Funktion eigentlich?
	public function theme_search_prompt( $theme, $criteria, $has_results )
	{
		$out=array();
		$keywords=explode(' ',trim($criteria));
		foreach ($keywords as $keyword) {
			$out[]= '<a href="' . Site::get_url( 'habari', true ) .'search?criteria=' . $keyword . '" title="' . _t( 'Search for ', $theme->name ) . $keyword . '">' . $keyword . '</a>';
		}

		if ( sizeof( $keywords ) > 1 ) {
			if ( $has_results ) {
				return sprintf( _t( 'Deine Suche nach "<b>%s</b>".', $theme->name ), implode(' ',$out) );
				exit;
			}
			return sprintf( _t('Keine Ergebnisse für deine Suche \'%1$s\'', $theme->name) . '<br />'. _t('You can try searching for \'%2$s\'', $theme->name), $criteria, implode('\' or \'',$out) );
		}
		else {
			return sprintf( _t( 'Suchergebnisse f&uuml;r "<b>%s</b>".', $theme->name ), $criteria );
			exit;
		}
		return sprintf( _t( 'Deine Suche nach "<b>%s</b>".', $theme->name ), $criteria );

	}
	
	// Get images
	// Use content_out when calling to only get the images from the excerpt
	public function post_get_images($content)
	{
		// TODO: Get surrounding links! (optional)
		preg_match_all("/<img[^>]+>/u", $content, $matches);

		// Create an array containing the image's source code, its path and all its classes
		$imagelist = array();
		
		foreach($matches[0] as $match)
		{
			$imagearray = array();
			$imagearray["original"] = $match; // save the original html
			
			// Get the image's source (extract the src part)
			$srcoffset = stripos($match, "src=\"");
			$imagearray["source"] = substr($match, $srcoffset+5, stripos($match, "\"", $srcoffset+5) - $srcoffset - 5);
			
			// Get the class(es), if any (extract the class part)
			// TODO: error handling when there are no classes
			$classoffset = stripos($match, "class=\"");
			$imagearray["classstring"] = substr($match, $classoffset+7, stripos($match, "\"", $classoffset+7) - $classoffset-7);
						
			// Stop now if classes cause exclusion of this image, otherwise add the classes to the array
			$classesarray = explode(' ', $imagearray["classstring"]);
			
			foreach($this->excludedclasses as $excluded)
			{
				if(in_array($excluded, $classesarray)) $imagearray = null;
			}
			if($imagearray == null) continue;
			else $imagearray["classes"] = $classesarray;
			
			// Finally, add the html without classes to remove (TODO - currently all classes are removed)
			$imagearray["out"] = str_replace("class=\"$classes\"", "", $match);
			
			$imagelist[] = $imagearray;
		}
		
		return $imagelist;
	}
	
	// Build a string with the post's meta, regarding the content type and which meta is available
	public function theme_metaline($theme, $post)
	{
		// TODO: Fallback!
		$this->assign("post", $post);
		return $this->fetch("metaline.".Post::type_name($post->content_type));
	}
	
	/**
	 * Add the options per post
	 * TODO: Actually perform the check if the per-post setting is set and if not, load the theme setting
	 */
	public function action_form_publish($form, $post, $context)
	{
		// add text fields
		$form->insert('tags', 'text', 'viewinsidephoto', 'null:null', _t('Sidephotos, max width 220px', $theme->name), 'admincontrol_textArea');
		$form->insert('tags', 'text', 'viewinsidephotosource', 'null:null', _t('The photos\' source', $theme->name), 'admincontrol_textArea');
		
		// add settings container and checkboxes
		$viewinsidefields = $form->publish_controls->append('fieldset', 'viewinsidefields', _t('TheViewInside', $theme->name));
		$viewinsidefields->append('checkbox', 'extract_images', 'extract_images', _t('Extract images from sourcecode', $theme->name));
		$viewinsidefields->append('checkbox', 'remove_images', 'remove_images', _t('Remove images from sourcecode', $theme->name));
		$viewinsidefields->append('text', 'max_images', 'max_images', _t('Max number of images in sidebar', $theme->name));
		
		// load values and display the fields
		$form->viewinsidephoto->value = $post->info->viewinsidephoto;
		$form->viewinsidephoto->template = 'admincontrol_text';
		$form->viewinsidephotosource->value = $post->info->viewinsidephotosource;
		$form->viewinsidephotosource->template = 'admincontrol_text';
		if(isset($post->info->extract_images)) $viewinsidefields->extract_images->value = $post->info->extract_images;
		$viewinsidefields->extract_images->template = 'tabcontrol_checkbox';
		if(isset($post->info->remove_images)) $viewinsidefields->remove_images->value = $post->info->remove_images;
		$viewinsidefields->remove_images->template = 'tabcontrol_checkbox';
		$viewinsidefields->max_images->value = $post->info->max_images;
		$viewinsidefields->max_images->template = 'tabcontrol_text';
		
	}
	
	// Save the photo fields
	public function action_publish_post( $post, $form )
	{
		$post->info->viewinsidephoto = $form->viewinsidephoto->value;
		$post->info->viewinsidephotosource = $form->viewinsidephotosource->value;
		$post->info->extract_images = $form->extract_images->value;
		$post->info->remove_images = $form->remove_images->value;
		$post->info->max_images = $form->max_images->value;
	}
	
	public function get_picasa_images($albumname, $userid = null, $size = "s220")
	{
		try
		{
			$picasa = new Picasa();
			$picasa->userid = $userid;
			
			// Get these crappy album IDs and the links
			$xml = $picasa->get_albums();
			
			foreach($xml->channel->item as $album)
			{
				$albumids[(string)$album->title] = (string)$album->children('http://schemas.google.com/photos/2007')->id;
				$albumlinks[(string)$album->title] = (string)$album->link;
			}
					
			$xml = $picasa->get_photos(array("album" => $albumids[$albumname]));
			
			// Get the actual photos
			foreach($xml->entry as $photo)
			{
				// Warum drei URLs?
				// TODO: Sind die nächsten drei Zeilen nicht eig über?
				// $media = $photo->children('http://search.yahoo.com/mrss/');
				// $props['thumbnail_url'] = (string)$media->group->thumbnail->attributes()->url;
				// $props['title'] = (string)$media->group->title;
				$src = (string)$photo->content->attributes()->src;
				$props['url'] = substr($src,0,strrpos($src,'/'))."/$size".substr($src,strrpos($src,'/'));
				$props['picasa_url'] = $src;
				
				$photos[] = $props;
			} 
		}
		catch(exception $e) { $photos = _t(vsprintf("No photos available or an error occured. Sometimes reloading the page helps. %s", $e), $theme->name); }

		return $photos;
	}
	
	function filter_post_tvi_photos($out, $post)
	{
		// Discard values from other plugins (usually, the $out parameter should be empty)
		$out = array();
		
		// 0. Images from content?
		if($post->info->extract_images)
		{
			$postimages = $this->post_get_images($post->content);
			if(count($postimages))
			{
				$out = array_merge($out, $postimages);
			}
		}
		
		// 1. No image?
		if(empty($post->info->viewinsidephoto))
		{
			// Try to get one via slug from the banner folder
			$slugphotos = array();
			// TODO: multiple numbered Slugphotos
			foreach($this->image_extensions as $ext)
			{
				$subpath = "/files/banner/" . $post->slug . $ext;
				if(is_file(Site::get_dir('user').$subpath))
					$slugphotos[] = Site::get_url('user').$subpath;
			}
			$out = array_merge($out, $slugphotos);
		}
		// 2. External image?
		else if(substr($post->info->viewinsidephoto,0,4)=="http")
		{
			// TODO: Multiple images!
			$out[] = $post->info->viewinsidephoto;
		}
		// 3. Picasa album?
		else if(substr($post->info->viewinsidephoto,0,7)=="picasa:")
		{
			$out = array_merge($out, $this->get_picasa_images(substr($post->info->viewinsidephoto,7), $post->user_id));
		}
		// 4. Internal image in user directory?
		else if(is_file(Site::get_dir('user').$post->info->viewinsidephoto))
		{
			// TODO: Multiple images!
			$out[] = Site::get_url('user').$post->info->viewinsidephoto;
		}
		
		// Apply limit and random order
		// TODO: Decide when to randomize here
		//$fu = count($out);
		//Utils::debug($out);
		$randomizedphotos = array();
		if(!isset($post->info->max_images) || $post->info->max_images == null || $post->info->max_images == 0)
			$randomkeys = array_rand($out, count($out));
		else
			$randomkeys = array_rand($out, $post->info->max_images);
		foreach((array)$randomkeys as $randomkey) $randomizedphotos[] = $out[$randomkey];
		
		return $randomizedphotos;
	}
	
	function filter_post_tvi_picasaalbum($out, $post)
	{
		if(substr($post->info->viewinsidephoto,0,7)=="picasa:") return substr($post->info->viewinsidephoto,7); else return "";
	}
	
	function filter_post_tvi_picasaalbum_out($out, $post)
	{
		$out = "";
		if(substr($post->info->viewinsidephoto,0,7)=="picasa:")
		{
			try
			{
				$picasa = new Picasa();
			
				// Get these crappy album IDs
				$xml = $picasa->get_albums();
				
				foreach($xml->channel->item as $album)
				{
					$albumids[(string)$album->title] = (string)$album->children('http://schemas.google.com/photos/2007')->id;
					$albumlinks[(string)$album->title] = (string)$album->link;
				}
				
				$out = $albumlinks[$post->tvi_picasaalbum];
			}
			catch(exception $e) { $out = _t(vsprintf("No Picasa album available or an error occured. Sometimes reloading the page helps. %s", $e), $theme->name); }
		}
		return $out;
	}
	
	function filter_post_tvi_photosources($out, $post)
	{
		if(!empty($post->info->viewinsidephotosource))
			return explode(';', $post->info->viewinsidephotosource);
		else return null;
	}
	
	function filter_post_tvi_photos_out($out, $post)
	{
		return $this->fetch("sidephotos");
	}
	
	function filter_post_isguestpost($out, $post)
	{
		return $post->author->displayname != "Konzertheld";
	}
}
?>