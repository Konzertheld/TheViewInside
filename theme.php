<?php
/**
 * TheViewInside Habari Theme
 * created by Konzertheld
 * http://konzertheld.de
 * made for the Habari community
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
		Format::apply('autop', 'comment_content_out');
		$this->add_template( 'block.tvipages', dirname(__FILE__) . '/block.tvipages.php' );
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
			if(!$morepos)
				$morepos = stripos($content, "<!-- more -->");
			if($morepos != false)
				$newcontent = substr($content, 0, $morepos) . "<p><a class='readmorelink' href='" . $post->permalink . "'>". _t("Continue reading %s", array($post->title), __CLASS__) ."</a></p>";
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
		preg_match_all("@<a [^>]*?rel=[^>]*>(<img[^>]+>)</a>@u", $content, $matches);

		// Create an array containing the image's source code, its path and all its classes
		$imagelist = array();
		
		foreach($matches[0] as $match)
		{
			$image = array();
			$image["original"] = $match; // save the original html
			
			// Get the image's source (extract the src part)
			$srcoffset = stripos($match, "src=\"");
			$image["source"] = substr($match, $srcoffset+5, stripos($match, "\"", $srcoffset+5) - $srcoffset - 5);
			
			$imagelist[] = $image;
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
		if ($form->content_type->value == Post::type('page'))
		{
			$form->insert('tags', 'text', 'viewinsidedescription', 'null:null', _t('Short description, by default used in the page block', __CLASS__), 'admincontrol_text');
		}
				
		// add settings container and checkboxes
		$viewinsidefields = $form->publish_controls->append('fieldset', 'viewinsidefields', _t('TheViewInside', __CLASS__));
		$viewinsidefields->append('checkbox', 'extract_images', 'extract_images', _t('Extract images from sourcecode', __CLASS__));
		$viewinsidefields->append('checkbox', 'remove_images', 'remove_images', _t('Remove images from sourcecode', __CLASS__));
		$viewinsidefields->append('text', 'max_images', 'max_images', _t('Max number of images in sidebar', __CLASS__));
		
		// load values and display the fields and if necessary fill them with initial values
		if(isset($post->info->extract_images))
			$viewinsidefields->extract_images->value = $post->info->extract_images;
		else
			$viewinsidefields->extract_images->value = false;
		$viewinsidefields->extract_images->template = 'tabcontrol_checkbox';
		if(isset($post->info->remove_images))
			$viewinsidefields->remove_images->value = $post->info->remove_images;
		else
			$viewinsidefields->remove_images->value = false;
		$viewinsidefields->remove_images->template = 'tabcontrol_checkbox';
		$viewinsidefields->max_images->value = $post->info->max_images;
		$viewinsidefields->max_images->template = 'tabcontrol_text';
		if ($form->content_type->value == Post::type('page'))
		{
			if(isset($post->info->viewinsidedescription))
				$form->viewinsidedescription->value = $post->info->viewinsidedescription;
			else
				$form->viewinsidedescription->value = "";
		}
	}
	
	/**
	 * Save the fields from the publish form
	 * Also extract images if requested for later
	 */
	public function action_publish_post( $post, $form )
	{		
		$post->info->extract_images = $form->extract_images->value;
		$post->info->remove_images = $form->remove_images->value;
		$post->info->max_images = $form->max_images->value;
		if ($form->content_type->value == Post::type('page'))
		{
			$post->info->viewinsidedescription = $form->viewinsidedescription->value;
		}
		
		// Extract images for the post's sidebar if the user requested it
		if($post->info->extract_images)
		{
			$imagelist = array();
			
			$images = $this->post_get_images($post->content);
			if(count($images))
			{
				foreach($images as $image)
				{
					$imagelist[] = $image["original"];
				}
			}
			
			$post->info->tvi_imagelist = $imagelist;
		}
	}
	
	/**
	 * Randomize extracted images and return them, taking care of the limit
	 */
	function filter_post_tvi_photos($out, $post)
	{
		// Discard values from other plugins (which should not exist)
		$out = $post->info->tvi_imagelist;
		if(!is_array($out)) return array();
		
		// Apply limit and random order
		// TODO: Decide when to randomize here
		if(isset($post->info->max_images) && !empty($post->info->max_images))
			$limit = $post->info->max_images;
		else
			$limit = 2; //TODO: Calculate a limit
		shuffle($out);
		return array_slice($out, 0, $limit);
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
	
	// Block section
	
	public function filter_block_list( $blocklist )
	{
		$blocklist[ 'tvipages' ] = _t( 'TheViewInside pages' );
		return $blocklist;
	}
	
	public function action_block_form_tvipages( $form, $block )
	{
		$pages = Posts::get(array('content_type' => Post::type('page'), 'status' => Post::status('published')));
		foreach($pages as $page)
		{
			$pageoptions[$page->id] = $page->title;
		}
		$form->append( 'select', 'pages', __CLASS__ . '__pageblock_pages', _t( 'Pages to display:', __CLASS__ ) );
		$form->pages->size = (count($pages) > 6) ? 6 : count($pages);
		$form->pages->multiple = true;
		$form->pages->options = $pageoptions;
	}
	
	public function action_block_content_tvipages( $block )
	{
		$pages = Options::get(__CLASS__ . '__pageblock_pages');
		$pageposts = Posts::get(array('id' => $pages));
		//$block->stuff = $pages;
		$block->pages = $pageposts;
	}
}
?>