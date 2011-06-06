<?php
/** This entire theme is based on TwentyTen from WordPress 3.1. Edited as I saw fit ****/

/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):

function twentyten_setup() {
    
    // Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories. More info at http://codex.wordpress.org/Post_Formats
	add_theme_support( 'post-formats', array( 'aside', 'audio', 'quote', 'link', 'image', 'video' ) );
	add_theme_support( 'post-thumbnails' ); // This theme uses Featured Images
	add_theme_support( 'automatic-feed-links' ); // Add default posts and comments RSS feed links to head

	// This theme uses wp_nav_menu() in one location. Add more as needed
	register_nav_menus( array( 'primary' => __( 'Primary Navigation', 'twentyten' ), ) );
}
endif;

/** Sets the post excerpt length to 40 characters. */
function twentyten_excerpt_length( $length ) { return 40; }
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/** Returns a "Continue Reading" link for excerpts. */
function twentyten_continue_reading_link() {
	return '<a href="'. get_permalink() . '"> Continue reading <span class="meta-nav">&rarr;</span></a>';
}

/** Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link(). */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/** Adds a pretty "Continue Reading" link to custom post excerpts. */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/** Remove inline styles printed when the gallery shortcode is used. */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/** Template for comments and pingbacks. */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( '%s <span class="says">says:</span>', sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php echo 'Your comment is awaiting moderation.'; ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( '%1$s at %2$s', get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( '(Edit)', ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
		break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p>Pingback: <?php comment_author_link(); ?><?php edit_comment_link( '(Edit)', ' ' ); ?></p>
	<?php
		break;
	endswitch;
}
endif;

/** Prints HTML with meta information for the current post—date/time and author. */
function twentyten_posted_on() {
	printf( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s',
	'meta-prep meta-prep-author', //%1$s
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date"><time datetime="%3$s">%4$s</time></span></a>',
			get_permalink(), //inner %1$s
			esc_attr( get_the_time() ),//inner %2$s
			get_the_time('c'),//inner %3$s
			get_the_date()//inner %4$s
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( 'View all posts by %s', get_the_author() ),
			get_the_author()
		) //%3$s
	);
}

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/** Prints HTML with meta information for the current post (category, tags and permalink). */
function twentyten_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.';
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.';
	} else {
		$posted_in = 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.';
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

/*** Custom Post Types **********************************************/
//add_action( 'init', 'wpst_create_my_post_types' );

//Gallery is just an example. Change as needed.
function wpst_create_my_post_types() {
	register_post_type( 'GALLERY',
		array(
			'labels' => array(
			'name' => __( 'Galleries' ),
			'singular_name' => __( 'Gallery' ),
			'add_new' => __( 'New Gallery' ),
			'add_new_item' => __( 'Add New Gallery' ),
			'edit' => __( 'Change' ),
			'edit_item' => __( 'Change the Gallery' ),
			'new_item' => __( 'A New Gallery' ),
			'view' => __( 'See' ),
			'view_item' => __( 'See the Gallery' ),
			'search_items' => __( 'Search Galleries' ),
			'not_found' => __( 'No Gallery to display' ),
			'not_found_in_trash' => __( 'No Galleries discarded' ),
			'parent' => __( 'Parent Gallery' ),
			'_builtin' => false, // It's a custom post type, not built in!
			'rewrite' => array('slug' => 'gallery', 'with_front' => FALSE), // Permalinks format
			),
			'public' => true,
			'show_ui' => true,
			'description' => __( 'Galleries for displaying your work' ),
			'query_var' => true,
			'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
			//'menu_icon' => get_stylesheet_directory_uri() . '/images/images_icon.png',
		)
	);
}

// browser detection via body_class
add_filter('body_class','wpst_browser_body_class');

function wpst_browser_body_class($classes) {
    //WordPress global vars available.
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

    if($is_lynx)       $classes[] = 'lynx';
    elseif($is_gecko)  $classes[] = 'gecko';
    elseif($is_opera)  $classes[] = 'opera';
    elseif($is_NS4)    $classes[] = 'ns4';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE)     $classes[] = 'ie';
    else               $classes[] = 'unknown';

    if($is_iphone) $classes[] = 'iphone';
    
    return $classes;
}
// Customize footer text
function wpst_remove_footer_admin () {
    //echo "Your own text";
} 
 
//add_filter('admin_footer_text', 'wpst_remove_footer_admin');

/*** Default Settings Cleanup and Adding Goodies **************************/

/*
// Remove feed urls
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
*/

//removes version number
remove_action('wp_head', 'wp_generator');

/* adds the favicon/appleicon to the wp_head() call*/
function wpst_blog_favicon() { echo '<link rel="shortcut icon" href="'.get_bloginfo('url').'/favicon.ico" />'; }
add_action('wp_head', 'wpst_blog_favicon');

function wpst_apple_icon() { echo '<link rel="apple-touch-icon" href="'.get_bloginfo('url').'/apple-touch-icon.png" />'; }
add_action('wp_head', 'wpst_apple_icon');

//Disable EditURI and WLWManifest
function wpst_remheadlink() {
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
}
add_action('init', 'wpst_remheadlink');

//removes Admin bar
wp_deregister_script('admin-bar');
wp_deregister_style('admin-bar');
remove_action('wp_footer','wp_admin_bar_render',1000);

// Includes the widgets.php file that defines all widget based functions. Done to clean up this file Uncomment to use.
//require_once( TEMPLATEPATH . '/widgets.php' );
?>
