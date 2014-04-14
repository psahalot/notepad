<?php
/**
 * Notepad functions and definitions
 *
 * @package Notepad
 * @since Notepad 1.0
 */

require( get_stylesheet_directory() . '/inc/customizer.php' ); // new customizer options
require( get_stylesheet_directory() . '/inc/custom-header.php' ); // custom header support
/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Notepad 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 798; /* Default the embedded content width to 790px */


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since Notepad 1.0
 *
 * @return void
 */
if ( ! function_exists( 'notepad_setup' ) ) {
	function notepad_setup() {
		global $content_width;

		/**
		 * Make theme available for translation
		 * Translations can be filed in the /languages/ directory
		 * If you're building a theme based on Notepad, use a find and replace
		 * to change 'notepad' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'notepad', trailingslashit( get_template_directory() ) . 'languages' );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );

		// Create an extra image size for the Post featured image
		add_image_size( 'post_feature_full_width', 680, 300, true );
                
		// This theme uses wp_nav_menu() in one location
		register_nav_menus( array(
				'primary' => esc_html__( 'Primary Menu', 'notepad' )
			) );

		// This theme supports a variety of post formats
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );

		// Enable support for Custom Backgrounds
		add_theme_support( 'custom-background', array(
				// Background color default
				'default-color' => '363d41',
				// Background image default
				'default-image' => '',
                                'wp-head-callback' => 'notepad_background_callback'
                                
			) );
	}
}
add_action( 'after_setup_theme', 'notepad_setup' );


/**
 * Returns the Google font stylesheet URL, if available.
 *
 * The use of Droid Serif and Montserrat by default is localized. For languages that use characters not supported by the fonts, the fonts can be disabled.
 *
 * @since Notepad 1.2.5
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function notepad_fonts_url() {
	$fonts_url = '';
	$subsets = 'latin';

	/* translators: If there are characters in your language that are not supported by Droid Serif, translate this to 'off'.
	 * Do not translate into your own language.
	 */
	$pt_sans = _x( 'on', 'Droid Serif font: on or off', 'notepad' );

	/* translators: To add an additional Droid Serif character subset specific to your language, translate this to 'greek', 'cyrillic' or 'vietnamese'.
	 * Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Droid Serif font: add new subset (cyrillic)', 'notepad' );

	if ( 'cyrillic' == $subset )
		$subsets .= ',cyrillic';

	/* translators: If there are characters in your language that are not supported by Merriweather, translate this to 'off'.
	 * Do not translate into your own language.
	 */
	$montserrat = _x( 'on', 'Montserrat font: on or off', 'notepad' );

	if ( 'off' !== $pt_sans || 'off' !== $montserrat ) {
		$font_families = array();

		if ( 'off' !== $pt_sans )
			$font_families[] = 'Droid+Serif:400,400italic,700,700italic';

		if ( 'off' !== $montserrat )
			$font_families[] = 'Montserrat:400,700';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '|', $font_families ),
			'subset' => $subsets,
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
         
}


/**
 * Enqueue scripts and styles
 *
 * @since Notepad 1.0
 *
 * @return void
 */
function notepad_scripts_styles() {

	// Register and enqueue our icon font
	// We're using the awesome Font Awesome icon font. http://fortawesome.github.io/Font-Awesome
	wp_register_style( 'fontawesome', trailingslashit( get_template_directory_uri() ) . 'assets/css/font-awesome.min.css' , array(), '4.0.3', 'all' );
	wp_enqueue_style( 'fontawesome' );

	/*
	 * Load our Google Fonts.
	 *
	 * To disable in a child theme, use wp_dequeue_style()
	 * function mytheme_dequeue_fonts() {
	 *     wp_dequeue_style( 'notepad-fonts' );
	 * }
	 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
	 */
	$fonts_url = notepad_fonts_url();
	if ( !empty( $fonts_url ) ) {
		wp_enqueue_style( 'notepad-fonts', esc_url_raw( $fonts_url ), array(), null );
	}

	// Enqueue the default WordPress stylesheet
	wp_enqueue_style( 'style', get_stylesheet_uri(), array(), '1.2.3', 'all' );
        
        // Load Modernizr at the top of the document, which enables HTML5 elements and feature detects
        wp_enqueue_script( 'modernizr', trailingslashit( get_template_directory_uri() ) . 'assets/js/modernizr-2.7.1-min.js', array(), '2.7.1', false );
            
	// Adds JavaScript to pages with the comment form to support sites with threaded comments (when in use)
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Load jQuery Validation as well as the initialiser to provide client side comment form validation
	// Using the 1.11.0pre version as it fixes an error that causes the email validation to fire immediately when text is entered in the field
	// You can change the validation error messages below
	if ( is_singular() && comments_open() ) {
		wp_register_script( 'validate', trailingslashit( get_template_directory_uri() ) . 'assets/js/jquery.validate.min.1.11.0pre.js', array( 'jquery' ), '1.11.0', true );
		wp_register_script( 'commentvalidate', trailingslashit( get_template_directory_uri() ) . 'assets/js/comment-form-validation.js', array( 'jquery', 'validate' ), '1.11.0', true );

		wp_enqueue_script( 'commentvalidate' );
		wp_localize_script( 'commentvalidate', 'comments_object', array(
			'req' => get_option( 'require_name_email' ),
			'author'  => esc_html__( 'Please enter your name', 'notepad' ),
			'email'  => esc_html__( 'Please enter a valid email address', 'notepad' ),
			'comment' => esc_html__( 'Please add a comment', 'notepad' ) )
		);
	}

	// Include this script to envoke a button toggle for the main navigation menu on small screens
	wp_enqueue_script( 'small-menu', trailingslashit( get_template_directory_uri() ) . 'assets/js/small-menu.js', array( 'jquery' ), '20130130', true );
	
}
add_action( 'wp_enqueue_scripts', 'notepad_scripts_styles' );



//* Enqueue Backstretch script and prepare images for loading
add_action( 'wp_enqueue_scripts', 'notepad_enqueue_scripts' );
function notepad_enqueue_scripts() {

	//* Load scripts only if custom background is being used
	if ( ! get_background_image() )
		return;

	wp_enqueue_script( 'notepad-backstretch', get_bloginfo( 'stylesheet_directory' ) . '/assets/js/backstretch.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_script( 'notepad-backstretch-set', get_bloginfo('stylesheet_directory').'/assets/js/backstretch-set.js' , array( 'jquery', 'notepad-backstretch' ), '1.0.0' );

	wp_localize_script( 'notepad-backstretch-set', 'BackStretchImg', array( 'src' => str_replace( 'http:', '', get_background_image() ) ) );
}

//* Add custom background callback for background color
function notepad_background_callback() {

	if ( ! get_background_color() )
		return;

	printf( '<style>body { background-color: #%s; }</style>' . "\n", get_background_color() );

}

/**

 * If we go beyond the last page and request a page that doesn't exist,

 * force WordPress to return a 404.

 * See http://core.trac.wordpress.org/ticket/15770

 */

function custom_paged_404_fix( ) {

    global $wp_query;

    if ( is_404() || !is_paged() || 0 != count( $wp_query->posts ) )

        return;

    $wp_query->set_404();

    status_header( 404 );

    nocache_headers();

}

add_action( 'wp', 'custom_paged_404_fix' );



// Add and save meta boxes for post links
add_action( 'add_meta_boxes', 'notepad_meta_box_add' );
function notepad_meta_box_add() {
	add_meta_box( 'postaudio-box', __('Post format audio url', 'notepad'), 'notepad_meta_box_cb', 'post', 'side', 'high' );
	add_meta_box( 'postvideo-box', __('Post format video url', 'notepad'), 'notepad_meta_box_cc', 'post', 'side', 'high' );
}

function notepad_meta_box_cb( $post ) {
	$values = get_post_custom( $post->ID );
	$text_audiourl = isset( $values['audiourl'] ) ? esc_attr( $values['audiourl'][0] ) : '';
	wp_nonce_field( 'notepad_meta_box_nonce', 'meta_box_nonce' );
	?>
		<p>
			<input type="text" name="audiourl" id="audiourl" value="<?php echo $text_audiourl; ?>" />
		</p>
	<?php		
}

function notepad_meta_box_cc( $post ) {
	$values = get_post_custom( $post->ID );
	$text_videourl = isset( $values['videourl'] ) ? esc_attr( $values['videourl'][0] ) : '';
	wp_nonce_field( 'notepad_meta_box_nonce', 'meta_box_nonce' );
	?>
		<p>
			<input type="text" name="videourl" id="videourl" value="<?php echo $text_videourl; ?>" />
		</p>
	<?php		
}

add_action( 'save_post', 'notepad_meta_box_save' );

function notepad_meta_box_save( $post_id ) {
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'notepad_meta_box_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchords can only have href attribute
		)
	);
	
	// Probably a good idea to make sure the data is set		
	if( isset( $_POST['audiourl'] ) )
		update_post_meta( $post_id, 'audiourl', wp_kses( $_POST['audiourl'], $allowed ) );		

	if( isset( $_POST['videourl'] ) )
		update_post_meta( $post_id, 'videourl', wp_kses( $_POST['videourl'], $allowed ) );		

}


/**
 * Adds additional stylesheets to the TinyMCE editor if needed.
 *
 * @since Notepad 1.2.5
 *
 * @param string $mce_css CSS path to load in TinyMCE.
 * @return string The filtered CSS paths list.
 */
function notepad_mce_css( $mce_css ) {
	$fonts_url = notepad_fonts_url();

	if ( empty( $fonts_url ) ) {
		return $mce_css;
	}

	if ( !empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= esc_url_raw( str_replace( ',', '%2C', $fonts_url ) );

	return $mce_css;
}
add_filter( 'mce_css', 'notepad_mce_css' );


/**
 * Register widgetized areas
 *
 * @since Notepad 1.0
 *
 * @return void
 */
function notepad_widgets_init() {
	register_sidebar( array(
			'name' => esc_html__( 'Main Sidebar', 'notepad' ),
			'id' => 'sidebar-main',
			'description' => esc_html__( 'Appears in the sidebar on posts and pages except the optional Front Page template, which has its own widgets', 'notepad' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		) );
        
	register_sidebar( array(
			'name' => esc_html__( 'Footer #1', 'notepad' ),
			'id' => 'sidebar-footer1',
			'description' => esc_html__( 'Appears in the footer sidebar', 'notepad' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		) );

	register_sidebar( array(
			'name' => esc_html__( 'Footer #2', 'notepad' ),
			'id' => 'sidebar-footer2',
			'description' => esc_html__( 'Appears in the footer sidebar', 'notepad' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		) );

	register_sidebar( array(
			'name' => esc_html__( 'Footer #3', 'notepad' ),
			'id' => 'sidebar-footer3',
			'description' => esc_html__( 'Appears in the footer sidebar', 'notepad' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		) );

}
add_action( 'widgets_init', 'notepad_widgets_init' );


/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Notepad 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function notepad_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the blog name.
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		$title = "$title $sep " . sprintf( esc_html__( 'Page %s', 'notepad' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'notepad_wp_title', 10, 2 );


/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Notepad 1.0
 *
 * @param string html ID
 * @return void
 */
if ( ! function_exists( 'notepad_content_nav' ) ) {
	function notepad_content_nav( $nav_id ) {
		global $wp_query;
		$big = 999999999; // need an unlikely integer

		$nav_class = 'site-navigation paging-navigation';
		if ( is_single() ) {
			$nav_class = 'site-navigation post-navigation nav-single';
		}
		?>
		<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
			<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'notepad' ); ?></h3>

			<?php if ( is_single() ) { // navigation links for single posts ?>

				<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '<i class="fa fa-angle-left"></i>', 'Previous post link', 'notepad' ) . '</span> %title' ); ?>
				<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '<i class="fa fa-angle-right"></i>', 'Next post link', 'notepad' ) . '</span>' ); ?>

			<?php } 
			elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) { // navigation links for home, archive, and search pages ?>

				<?php echo paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, get_query_var( 'paged' ) ),
					'total' => $wp_query->max_num_pages,
					'type' => 'list',
					'prev_text' => wp_kses( __( '<i class="fa fa-angle-left"></i> Previous', 'notepad' ), array( 'i' => array( 
						'class' => array() ) ) ),
					'next_text' => wp_kses( __( 'Next <i class="fa fa-angle-right"></i>', 'notepad' ), array( 'i' => array( 
						'class' => array() ) ) )
				) ); ?>

			<?php } ?>

		</nav><!-- #<?php echo $nav_id; ?> -->
		<?php
	}
}


/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own notepad_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 * (Note the lack of a trailing </li>. WordPress will add it itself once it's done listing any children and whatnot)
 *
 * @since Notepad 1.0
 *
 * @param array Comment
 * @param array Arguments
 * @param integer Comment depth
 * @return void
 */
if ( ! function_exists( 'notepad_comment' ) ) {
	function notepad_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) {
		case 'pingback' :
		case 'trackback' :
			// Display trackbacks differently than normal comments ?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="pingback">
					<p><?php esc_html_e( 'Pingback:', 'notepad' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'notepad' ), '<span class="edit-link">', '</span>' ); ?></p>
				</article> <!-- #comment-##.pingback -->
			<?php
			break;
		default :
			// Proceed with normal comments.
			global $post; ?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment">
					<header class="comment-meta comment-author vcard">
						<?php
						echo get_avatar( $comment, 44 );
						printf( '<cite class="fn">%1$s %2$s</cite>',
							get_comment_author_link(),
							// If current post author is also comment author, make it known visually.
							( $comment->user_id === $post->post_author ) ? '<span> ' . esc_html__( 'Post author', 'notepad' ) . '</span>' : '' );
						printf( '<a href="%1$s" title="Posted %2$s"><time itemprop="datePublished" datetime="%3$s">%4$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							sprintf( esc_html__( '%1$s @ %2$s', 'notepad' ), esc_html( get_comment_date() ), esc_attr( get_comment_time() ) ),
							get_comment_time( 'c' ),
							/* Translators: 1: date, 2: time */
							sprintf( esc_html__( '%1$s at %2$s', 'notepad' ), get_comment_date(), get_comment_time() )
						);
						?>
					</header> <!-- .comment-meta -->

					<?php if ( '0' == $comment->comment_approved ) { ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'notepad' ); ?></p>
					<?php } ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'notepad' ), '<p class="edit-link">', '</p>' ); ?>
					</section> <!-- .comment-content -->

					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => wp_kses( __( 'Reply <span>&darr;</span>', 'notepad' ), array( 'span' => array() ) ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div> <!-- .reply -->
				</article> <!-- #comment-## -->
			<?php
			break;
		} // end comment_type check
	}
}


/**
 * Update the Comments form so that the 'required' span is contained within the form label.
 *
 * @since notepad 1.0
 *
 * @param string Comment form fields html
 * @return string The updated comment form fields html
 */
function notepad_comment_form_default_fields( $fields ) {

	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? ' aria-required="true"' : "" );

	$fields[ 'author' ] = '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'notepad' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';

	$fields[ 'email' ] =  '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'notepad' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';

	$fields[ 'url' ] =  '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'notepad' ) . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';

	return $fields;

}
add_action( 'comment_form_default_fields', 'notepad_comment_form_default_fields' );


/**
 * Update the Comments form to add a 'required' span to the Comment textarea within the form label, because it's pointless 
 * submitting a comment that doesn't actually have any text in the comment field!
 *
 * @since notepad 1.0
 *
 * @param string Comment form textarea html
 * @return string The updated comment form textarea html
 */
function notepad_comment_form_field_comment( $field ) {

	$field = '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun', 'notepad' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

	return $field;

}
add_action( 'comment_form_field_comment', 'notepad_comment_form_field_comment' );


/**
 * Prints HTML with meta information for current post: author and date
 *
 * @since notepad 1.0
 *
 * @return void
 */
if ( ! function_exists( 'notepad_posted_on' ) ) {
	function notepad_posted_on() {
		$post_icon = '';
		switch ( get_post_format() ) {
			case 'aside':
				$post_icon = 'fa-file-o';
				break;
			case 'audio':
				$post_icon = 'fa-volume-up';
				break;
			case 'chat':
				$post_icon = 'fa-comment';
				break;
			case 'gallery':
				$post_icon = 'fa-camera';
				break;
			case 'image':
				$post_icon = 'fa-picture-o';
				break;
			case 'link':
				$post_icon = 'fa-link';
				break;
			case 'quote':
				$post_icon = 'fa-quote-left';
				break;
			case 'status':
				$post_icon = 'fa-user';
				break;
			case 'video':
				$post_icon = 'fa-video-camera';
				break;
			default:
				$post_icon = 'fa-calendar';
				break;
		}

		// Translators: 1: Icon 2: Permalink 3: Post date and time 4: Publish date in ISO format 5: Post date
		$date = sprintf( '<i class="fa %1$s"></i> <a href="%2$s" title="Posted %3$s" rel="bookmark"><time class="entry-date" datetime="%4$s" itemprop="datePublished">%5$s</time></a>',
			$post_icon,
			esc_url( get_permalink() ),
			sprintf( esc_html__( '%1$s @ %2$s', 'notepad' ), esc_html( get_the_date() ), esc_attr( get_the_time() ) ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		// Translators: 1: Date link 2: Author link 3: Categories 4: No. of Comments
		$author = sprintf( '<i class="fa fa-pencil"></i> <address class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></address>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( esc_html__( 'View all posts by %s', 'notepad' ), get_the_author() ) ),
			get_the_author()
		);

		// Return the Categories as a list
		$categories_list = get_the_category_list( esc_html__( ' ', 'notepad' ) );

		// Translators: 1: Permalink 2: Title 3: No. of Comments
		$comments = sprintf( '<span class="comments-link"><i class="fa fa-comment"></i> <a href="%1$s" title="%2$s">%3$s</a></span>',
			esc_url( get_comments_link() ),
			esc_attr( esc_html__( 'Comment on ' . the_title_attribute( 'echo=0' ) ) ),
			( get_comments_number() > 0 ? sprintf( _n( '%1$s Comment', '%1$s Comments', get_comments_number() ), get_comments_number() ) : esc_html__( 'No Comments', 'notepad' ) )
		);

		// Translators: 1: Date 2: Author 3: Categories 4: Comments
		printf( wp_kses( __( '<div class="header-meta">%1$s%2$s<span class="post-categories">%3$s</span>%4$s</div>', 'notepad' ), array( 
			'div' => array ( 
				'class' => array() ), 
			'span' => array( 
				'class' => array() ) ) ),
			$date,
			$author,
			$categories_list,
			( is_search() ? '' : $comments )
		);
	}
}


/**
 * Prints HTML with meta information for current post: categories, tags, permalink
 *
 * @since notepad 1.0
 *
 * @return void
 */
if ( ! function_exists( 'notepad_entry_meta' ) ) {
	function notepad_entry_meta() {
		// Return the Tags as a list
		$tag_list = "";
		if ( get_the_tag_list() ) {
			$tag_list = get_the_tag_list( '<span class="post-tags">', esc_html__( ' ', 'notepad' ), '</span>' );
		}

		// Translators: 1 is tag
		if ( $tag_list ) {
			printf( wp_kses( __( '<i class="fa fa-tag"></i> %1$s', 'notepad' ), array( 'i' => array( 'class' => array() ) ) ), $tag_list );
		}
                
              
	}
}


/**
 * Change the "read more..." link so it links to the top of the page rather than part way down
 *
 * @since notepad 1.0
 *
 * @param string The 'Read more' link
 * @return string The link to the post url without the more tag appended on the end
 */
function notepad_remove_more_jump_link( $link ) {
	$offset = strpos( $link, '#more-' );
	if ( $offset ) {
		$end = strpos( $link, '"', $offset );
	}
	if ( $end ) {
		$link = substr_replace( $link, '', $offset, $end-$offset );
	}
	return $link;
}
add_filter( 'the_content_more_link', 'notepad_remove_more_jump_link' );


/**
 * Returns a "Read more" link for excerpts
 *
 * @since notepad 1.0
 *
 * @return string The 'Read more' link
 */
function notepad_continue_reading_link() {
	return '&hellip;<p><a class="more-link" href="'. esc_url( get_permalink() ) . '" title="' . esc_html__( 'Read more', 'notepad' ) . get_the_title() . ' ">' . wp_kses( __( 'Read more <span class="meta-nav">&rarr;</span>', 'notepad' ), array( 'span' => array( 
			'class' => array() ) ) ) . '</a></p>';
}


/**
 * Replaces "[...]" (appended to automatically generated excerpts) with the notepad_continue_reading_link().
 *
 * @since notepad 1.0
 *
 * @param string Auto generated excerpt
 * @return string The filtered excerpt
 */
function notepad_auto_excerpt_more( $more ) {
	return notepad_continue_reading_link();
}
add_filter( 'excerpt_more', 'notepad_auto_excerpt_more' );


/**
 * Extend the user contact methods to include Twitter, Facebook and Google+
 *
 * @since notepad 1.0
 *
 * @param array List of user contact methods
 * @return array The filtered list of updated user contact methods
 */
function notepad_new_contactmethods( $contactmethods ) {
	// Add Twitter
	$contactmethods['twitter'] = 'Twitter';

	//add Facebook
	$contactmethods['facebook'] = 'Facebook';

	//add Google Plus
	$contactmethods['googleplus'] = 'Google+';

	return $contactmethods;
}
add_filter( 'user_contactmethods', 'notepad_new_contactmethods', 10, 1 );


/**
 * Add a filter for wp_nav_menu to add an extra class for menu items that have children (ie. sub menus)
 * This allows us to perform some nicer styling on our menu items that have multiple levels (eg. dropdown menu arrows)
 *
 * @since notepad 1.0
 *
 * @param Menu items
 * @return array An extra css class is on menu items with children
 */
function notepad_add_menu_parent_class( $items ) {

	$parents = array();
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[] = $item->menu_item_parent;
		}
	}

	foreach ( $items as $item ) {
		if ( in_array( $item->ID, $parents ) ) {
			$item->classes[] = 'menu-parent-item';
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'notepad_add_menu_parent_class' );


/**
 * Add Filter to allow Shortcodes to work in the Sidebar
 *
 * @since notepad 1.0
 */
add_filter( 'widget_text', 'do_shortcode' );

/** 
 * Additional settings for Easy Digital Downloads
 * 
 * @since notepad 1.0
 */


/**
 * Recreate the default filters on the_content
 * This will make it much easier to output the Theme Options Editor content with proper/expected formatting.
 * We don't include an add_filter for 'prepend_attachment' as it causes an image to appear in the content, on attachment pages.
 * Also, since the Theme Options editor doesn't allow you to add images anyway, no big deal.
 *
 * @since notepad 1.0
 */
add_filter( 'meta_content', 'wptexturize' );
add_filter( 'meta_content', 'convert_smilies' );
add_filter( 'meta_content', 'convert_chars'  );
add_filter( 'meta_content', 'wpautop' );
add_filter( 'meta_content', 'shortcode_unautop'  );


add_filter('body_class', 'notepad_body_classes');
function notepad_body_classes($classes) {
   
        $slug = strtolower(get_theme_mod('notepad_color_scheme'));
        $classes[] = 'notepad-' . $slug;
    
    return $classes; 
    
}