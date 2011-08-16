<?php
/**
 * TheViewInside Habari Theme
 * by Konzertheld
 * http://konzertheld.de
 */

class TheViewInside extends Theme
{
	var $defaultsettings = array(
		'content_types' => 'entry',
		'gpmulti' => 0,
		'gpsingle' => 1,
		'social_postfeed' => 1,
		'social_commentsfeed' => 0,
		'socialnets' => '',
		);
		
	var $image_extensions = array('.png', '.jpg');
	
	var $excludedclasses = array("rating");
	//$excludedclasses[] = "blockintextlinks";
	//$excludedclasses[] = "blockintextrechts";
	
	/**
	 * Set the default options if necessary.
	 * Avoids errors on first theme activation and on theme upgrade.
	 */
	function initialize_options()
	{
		$opts = Options::get_group(__CLASS__);
		if (empty($opts))
		{
			Options::set_group(__CLASS__, $this->defaultsettings);
		}
		else
		{
			foreach($this->defaultsettings as $defsettingi => $defsettingv)
			{
				if(empty($opts[$defsettingi]))
					$opts[$defsettingi] = $defsettingv;
			}
			Options::set_group(__CLASS__, $opts);
		}
	}
	
	/**
	 * Call initialization on theme activation.
	 */
	public function action_theme_activated()
	{
		$this->initialize_options();
	}
	
	/**
	 * Execute on theme init to apply these filters to output
	 */
	public function action_init_theme()
	{
		$this->initialize_options();
		
		// Apply Format::autop() to comment content
		Format::apply('autop', 'comment_content_out');
		
		$this->load_text_domain('TheViewInside');
	}
	
	public function action_admin_header($theme)
	{
		if ($theme->page == 'themes')
		{
			Stack::add('admin_stylesheet', array($this->get_url() . '/admin.css', 'screen'));
		}
	}
	
	/**
	 * Create theme options
	 */
	public function action_theme_ui( $theme )
	{
		$ui = new FormUI(__CLASS__);
		$ui->append('fieldset', 'general', _t('General settings', __CLASS__));
		// Get the available content types
		$types = Post::list_active_post_types();
		unset($types['any']);
		$types = array_flip($types);
		$ui->general->append( 'select', 'content_types', __CLASS__ . '__content_types', _t( 'Content Types in pagination:', __CLASS__ ) );
		$ui->general->content_types->size = count($types);
		$ui->general->content_types->multiple = true;
		$ui->general->content_types->options = $types;
		
		foreach(Users::get_all() as $user)
			$users[$user->id] = $user->displayname;
		$ui->general->append( 'select', 'default_authors', __CLASS__ . '__default_authors', _t('These authors are no guests:', __CLASS__ ));
		$ui->general->default_authors->size = count($users);
		$ui->general->default_authors->multiple = true;
		$ui->general->default_authors->options = $users;
		
		$ui->append('fieldset', 'social', _t('Social Icons', __CLASS__));
		$ui->social->append('textarea', 'socialnets', __CLASS__ . '__socialnets', _t('Social nets you are using, separated by commas', __CLASS__));
		$ui->social->socialnets->rows = 3;
		
		$opts = Options::get_group(__CLASS__);
		if(isset($opts['socialnets']))
		{
			$socialnetslist = explode(',', $opts['socialnets']);
			foreach($socialnetslist as $socialnet)
			{
				$c = $ui->social->append('text', Utils::slugify($socialnet) . '_img', __CLASS__ . '__' . Utils::slugify($socialnet) . '__img', _t('Image for %s', array($socialnet), __CLASS__));
				$c->class[] = "tvi_leftcol";
				$d = $ui->social->append('text', Utils::slugify($socialnet) . '_url', __CLASS__ . '__' . Utils::slugify($socialnet) . '__url', _t('URL for %s', array($socialnet), __CLASS__));
				$d->class[] = "tvi_rightcol";
			}
		}
		
		$ui->social->append('checkbox', 'social_postfeed', __CLASS__ . '__social_postfeed', _t('Show post feed in social area', __CLASS__));
		$ui->social->append('checkbox', 'social_commentsfeed', __CLASS__ . '__social_commentsfeed', _t('Show comments feed in social area', __CLASS__));
		$ui->social->append('checkbox', 'gpmulti', __CLASS__ . '__gpmulti', _t('Show Google +1 Button on multiple views:', __CLASS__));
		$ui->social->append('checkbox', 'gpsingle', __CLASS__ . '__gpsingle', _t('Show Google +1 Button on single views:', __CLASS__));
		
		// Save
		$ui->append( 'submit', 'save', _t( 'Save', __CLASS__ ) );
		$ui->on_success(array($this, 'options_callback'));
		$ui->out();
	}
	
	/**
	 * Redirect to the theme admin to force a full reload
	 * Required for the generated text fields to update
	 */
	function options_callback($form)
	{
		$form->save();
		Session::notice(_t('Options saved'));
		Utils::redirect(URL::get('admin', 'page=themes'));
		return false;
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

		$this->assign( 'multipleview', false);
		$action = Controller::get_action();
		if ($action == 'display_home' || $action == 'display_entries' || $action == 'search' || $action == 'display_tag' || $action == 'display_date') {
			$this->assign('multipleview', true);
		}

		$this->assign('controller_action', $action);
		
		// Use theme options to set values that can be used directly in the templates
		$opts = Options::get_group( __CLASS__ );
		$this->assign('content_types', $opts['content_types']);
		$this->assign('gpmulti', $opts['gpmulti']);
		$this->assign('gpsingle', $opts['gpsingle']);
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
				$newcontent = substr($content, 0, $morepos)."<p><a class='readmorelink' href='".$post->permalink."' title='".sprintf(_t("Continue reading %s", __CLASS__),$post->title)."'>".sprintf(_t("Continue reading %s", __CLASS__),$post->title)."</a></p>";
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
			
			// Get the class(es), if any (extract the class attribute)
			$classoffset = stripos($match, "class=\"");
			if($classoffset != false)
			{
				$imagearray["classstring"] = substr($match, $classoffset+7, stripos($match, "\"", $classoffset+7) - $classoffset-7);
							
				// Stop now if classes cause exclusion of this image, otherwise add the classes to the array
				$classesarray = explode(' ', $imagearray["classstring"]);
				
				foreach($this->excludedclasses as $excluded)
				{
					if(in_array($excluded, $classesarray)) $imagearray = null;
				}
				if($imagearray == null) continue;
				else $imagearray["classes"] = $classesarray;
			}
			
			// Finally, add the html without classes. If some classes are needed, they can be added again later.
			$imagearray["out"] = str_replace("class=\"$classes\"", "", $match);
			
			$imagelist[] = $imagearray;
		}
		
		return $imagelist;
	}
	
	public function theme_socialneticons($theme)
	{
		$out = "";
		$opts = Options::get_group(get_class($theme));
		if(isset($opts['socialnets']))
		{
			$socialnetslist = explode(',', $opts['socialnets']);
			foreach($socialnetslist as $socialnet)
			{
				$socialurl = $opts[Utils::slugify($socialnet) . '__url'];
				if(!empty($socialurl))
				{
					$out .= "<a href='$socialurl' id='net-" . Utils::slugify($socialnet) . "' title='$socialnet' class='socialneticon'></a>";
				}
			}
		}
		if($opts['social_postfeed'])
			$out .= "<a href='" . URL::get('atom_feed', array('index' => 1)) . "' id='net-postfeed' title='" . _t('Atom feed for posts', __CLASS__) . "' class='socialneticon'></a>";
		if($opts['social_commentsfeed'])
			$out .= "<a href='" . URL::get('atom_feed_comments') . "' id='net-commentsfeed' title='" . _t('Atom feed for comments', __CLASS__) . "' class='socialneticon'></a>";
		return $out;
	}
	
	/**
	 * Build a string with the post's meta, regarding the content type and which meta is available
	 * Reassign $post so it always contains the current post in multiple views
	 */
	public function theme_metaline($theme, $post)
	{
		// TODO: Fallback!
		$this->assign("post", $post);
		return $this->fetch("metaline.".Post::type_name($post->content_type));
	}
	
	/**
	 * Add the options per post
	 */
	public function action_form_publish($form, $post, $context)
	{
		// add text fields
		$form->insert('tags', 'text', 'viewinsidephoto', 'null:null', _t('Sidephotos, max width 220px', __CLASS__), 'admincontrol_textArea');
		$form->insert('tags', 'text', 'viewinsidephotosource', 'null:null', _t('The photos\' source', __CLASS__), 'admincontrol_textArea');
		
		// add settings container and checkboxes
		$viewinsidefields = $form->publish_controls->append('fieldset', 'viewinsidefields', _t('TheViewInside', __CLASS__));
		$viewinsidefields->append('checkbox', 'extract_images', 'extract_images', _t('Extract images from sourcecode', __CLASS__));
		$viewinsidefields->append('checkbox', 'remove_images', 'remove_images', _t('Remove images from sourcecode', __CLASS__));
		$viewinsidefields->append('text', 'max_images', 'max_images', _t('Max number of images in sidebar', __CLASS__));
		
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
	
	function filter_post_tvi_photos($out, $thispost)
	{
		// TODO: Use caching or something else so that this function is only executed once even though there are two calls in the post template
		// TODO: Outsource this into a block. Block options should then be all these points here. Picasa would be an option then, too, which makes this function check $post->picasa_images
		// Discard values from other plugins (usually, the $out parameter should be empty)
		$out = array();
		
		// 0. Images from content?
		if($thispost->info->extract_images)
		{
			$thispostimages = $this->post_get_images($thispost->content);
			if(count($thispostimages))
			{
				$out = array_merge($out, $thispostimages);
			}
		}
		
		// 1. No image?
		if(empty($thispost->info->viewinsidephoto))
		{
			// Try to get one via slug from the banner folder
			$slugphotos = array();
			// TODO: multiple numbered Slugphotos
			foreach($this->image_extensions as $ext)
			{
				$subpath = "/files/banner/" . $thispost->slug . $ext;
				if(is_file(Site::get_dir('user').$subpath))
					$slugphotos[] = Site::get_url('user').$subpath;
			}
			$out = array_merge($out, $slugphotos);
		}
		// 2. External image?
		if(substr($thispost->info->viewinsidephoto,0,4)=="http")
		{
			// TODO: Multiple images!
			$out[] = $thispost->info->viewinsidephoto;
		}
		// 3. Picasa album
		$picasaimages = $thispost->picasa_images;
		if(count($picasaimages))
		{
			$out = array_merge($out, $thispost->picasa_images);
		}
		// 4. Internal image in user directory?
		if(is_file(Site::get_dir('user').$thispost->info->viewinsidephoto))
		{
			// TODO: Multiple images!
			$out[] = Site::get_url('user').$thispost->info->viewinsidephoto;
		}
		
		// Apply limit and random order
		// TODO: Decide when to randomize here
		if(isset($thispost->info->max_images) && !empty($thispost->info->max_images))
			$limit = $thispost->info->max_images;
		else $limit = 1; //TODO: Calculate a limit
		shuffle($out);
		$randomizedphotos = array_slice($out, 0, $limit);
		return $randomizedphotos;
	}
	
	function filter_post_tvi_photosources($out, $post)
	{
		if(!empty($post->info->viewinsidephotosource))
			return explode(';', $post->info->viewinsidephotosource);
		else return null;
	}
	
	function filter_post_tvi_photos_out($out, $post)
	{
		$this->assign('content', $post);
		return $this->fetch("sidephotos");
	}
	
	function filter_post_isguestpost($out, $post)
	{
		$opts = Options::get_group( __CLASS__ );
		return !in_array($post->author->id, $opts['default_authors']);
	}
}
?>