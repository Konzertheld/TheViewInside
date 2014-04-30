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
	 * Grab variables, translations and formats on initialization
	 */
	public function action_init_theme()
	{
		Format::apply('autop', 'comment_content_out');
		$this->load_text_domain('TheViewInside');
		
		$this->assign( 'multipleview', false);
		$action = Controller::get_action();
		if ($action == 'display_home' || $action == 'display_entries' || $action == 'search' || $action == 'display_tag' || $action == 'display_date') {
			$this->assign('multipleview', true);
		}

		$this->assign('controller_action', $action);
		
		// Use theme options to set values that can be used directly in the templates
		$opts = Options::get_group( __CLASS__ );
		$this->assign('content_types', $opts['content_types']);
	}
	
	public function filter_rewrite_rules($db_rules)
	{
		$rule = RewriteRule::create_url_rule('"archives"/"type"/type', 'UserThemeHandler', 'display_posts_by_type');
		$db_rules[] = $rule;
	}
	
	/**
	 * Handle incoming /archives/type/$type requests
	 */
	public function action_handler_display_posts_by_type($handler)
	{
		$this->assign('multipleview', true);
		$params = array(
			'nolimit' => '',
			'orderby' => 'title ASC',
			'status' => Post::status( 'published' ),
			'content_type' => $handler['type'],
		);
		$posts = Posts::get($params);
		$this->assign('posts', $posts);
		$this->act_display(array("posts" => $posts));
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
	 * Convert a post's tags ArrayObject into a usable list of links
	 *
	 * @param array $array The Tags object from a Post object
	 * @return string The HTML of the linked tags
	 */
	public function filter_post_tags_out($array)
	{
		foreach($array as $tag) $array_out[] = "<li><a href='" . URL::get("display_entries_by_tag", array( "tag" => $tag->term) ) . "' rel='tag'>" . $tag->term_display . "</a></li>\n";
		$out = '<ul>' . implode('', $array_out) . '</ul>';
		return "<p>" . _t("This post and more are filed under", __CLASS__) . "</p>$out";
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
		//@TODO: Cache this. Also, it's called in too many places and images are stored as postinfo. wtf?
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
		$this->assign("post", $post);
		if ($this->template_exists("metaline." . Post::type_name($post->content_type))) {
			return $this->fetch("metaline." . Post::type_name($post->content_type));
		}
		else {
			return $this->fetch("metaline.entry");
		}
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
		$viewinsidefields->append('text', 'tvi_photosource', 'tvi_photosource', _t('Photo(s) to use for the view inside', __CLASS__));
		
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
		$viewinsidefields->tvi_photosource->value = $post->info->tvi_photosource;
		$viewinsidefields->tvi_photosource->template = 'tabcontrol_text';
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
		$post->info->tvi_photosource = $form->tvi_photosource->value;
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
			if(count($imagelist)) {
				$post->info->tvi_imagelist = $imagelist;
			}
			else {
				unset($post->info->tvi_imagelist);
			}
		}
		else
		{
			unset($post->info->tvi_imagelist);
		}
	}
	
	/**
	 * Add a link to all the silos to use the current path for the sidephotos
	 */
	public function filter_media_controls($controls, $silo, $rpath)
	{
		// We can only get the path from an asset and we can only grab assets through iteration. So we do that and break immediately.
		$controls['use_for_tvi'] = '<a href="#" onclick="for(var key in habari.media.assets) { $(\'#tvi_photosource\').val(habari.media.assets[key].path.slice(0,-habari.media.assets[key].basename.length-1)); break; }">' . _t( 'Use for sidephotos' ) . '</a>' ;
		
		return $controls;
	}
	
	/**
	 * Convenience function to save time. We could just do a count() on the real photo function
	 * but that would require collecting all the photos just for checking,
	 * so we rather just look if there is something.
	 */
	public function filter_post_tvi_hasphotos($out, $post)
	{	
		$extracted = $post->info->tvi_imagelist;
		$photosource = $post->info->tvi_photosource;
		return ((isset($post->info->tvi_imagelist) && !empty($post->info->tvi_imagelist)) || (isset($photosource) && !empty($photosource)));
	}
	
	/**
	 * Randomize extracted images and return them, taking care of the limit
	 */
	function filter_post_tvi_photos($out, $post)
	{
		// Discard values from other plugins (which should not exist)
		// Replace them with the list of extracted images (which is generated when a post is saved)
		$out = $post->info->tvi_imagelist;
		
		// Make sure we use an array to avoid errors
		if(!is_array($out)) {
			$out = array();
		}
		
		// Next, grab media from the linked silo directory (if any)
		$photosource = $post->info->tvi_photosource;
		if(isset($photosource) && !empty($photosource)) {
			$assets = Media::dir($photosource);
			// Create and assign thumbnails
			foreach($assets as $asset) {
				$asset->thumbnail = $this->create_thumbnail($asset);
				$out[] = $asset;
			}
		}
			
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

	// Helper functions
	
	// Will become obsolete in 0.10
	public function create_thumbnail($asset)
	{
		$src_filename = $asset->url;
		$max_width = 220;
		
		// Does derivative directory not exist?
		$urldir = dirname( $src_filename ) . '/.tvi_thumbs';
		$parts = parse_url($urldir);
		$thumbdir = HABARI_PATH . $parts['path'];
		
		if ( !is_dir( $thumbdir ) ) {
			// Create the derivative driectory
			if ( !mkdir( $thumbdir, 0755 ) ) {
				// Couldn't make derivative directory
				return $asset->url;
			}
		}
		
		// Define the thumbnail filename
		$dst_filename = $thumbdir . '/' . basename( $src_filename ) . ".thumbnail.jpg";
		$dst_url = $urldir . '/' . basename( $src_filename ) . ".thumbnail.jpg";
		
		// If the file already exists, return
		if(is_file($dst_filename)) {
			return $dst_url;
		}

		// Get information about the image
		$isize = @getimagesize( $src_filename );
		if(is_array($isize)) {
			list( $src_width, $src_height, $type, $attr )= $isize;
		}
		else {
			$type = '';
			$src_img = '';
		}

		// Load the image based on filetype
		switch ( $type ) {
		case IMAGETYPE_JPEG:
			$src_img = imagecreatefromjpeg( $src_filename );
			break;
		case IMAGETYPE_PNG:
			$src_img = imagecreatefrompng( $src_filename );
			break;
		case IMAGETYPE_GIF:
			$src_img = imagecreatefromgif ( $src_filename );
			break;
		default:
			return $asset->url;
		}
		// Did the image fail to load?
		if ( !$src_img ) {
			return $asset->url;
		}

		// Calculate the output size based on the original's aspect ratio
		$thumb_w = $max_width;
		$thumb_h = $src_height * $max_width / $src_width;

		// Create the output image and copy the source to it
		$dst_img = ImageCreateTrueColor( $thumb_w, $thumb_h );
		imagecopyresampled( $dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_width, $src_height );

		// Save the thumbnail as a JPEG
		imagejpeg( $dst_img, $dst_filename );

		// Clean up memory
		imagedestroy( $dst_img );
		imagedestroy( $src_img );
		
		return $dst_url;
	}
}
?>