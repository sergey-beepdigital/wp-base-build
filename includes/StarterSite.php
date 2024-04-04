<?php

use Timber\Site;

/**
 * Class StarterSite
 */
class StarterSite extends Site {
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_filter( 'timber/twig/environment/options', [ $this, 'update_twig_environment_options' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'assets' ] );

		// remove emoji support
		add_action( 'init', [ $this, 'disable_emojis' ] );

		// remove comments menu item
		add_action( 'admin_menu', [ $this, 'remove_menus' ] );

		// set branding
		add_action( 'wp_before_admin_bar_render', [ $this, 'beep_admin_logo' ] );

		// remove RSS feeds, show homepage
		add_action( 'do_feed', [ $this, 'disable_feeds' ], - 1 );
		add_action( 'do_feed_rdf', [ $this, 'disable_feeds' ], - 1 );
		add_action( 'do_feed_rss', [ $this, 'disable_feeds' ], - 1 );
		add_action( 'do_feed_rss2', [ $this, 'disable_feeds' ], - 1 );
		add_action( 'do_feed_atom', [ $this, 'disable_feeds' ], - 1 );

		// disable comment feeds (optional)
		add_action( 'do_feed_rss2_comments', [ $this, 'disable_feeds' ], - 1 );
		add_action( 'do_feed_atom_comments', [ $this, 'disable_feeds' ], - 1 );

		// prevent feed links in page <head>
		add_action( 'feed_links_show_posts_feed', '__return_false', - 1 );
		add_action( 'feed_links_show_comments_feed', '__return_false', - 1 );
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );

		// remove unnecessary WP links
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'start_post_rel_link' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		add_filter( 'xmlrpc_enabled', '__return_false' );

		parent::__construct();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {

	}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies() {

	}

	/**
	 * This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = Timber::get_menu();
		$context['site']  = $this;

		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}

	/**
	 * his would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @param Twig\Environment $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		/**
		 * Required when you want to use Twig’s template_from_string.
		 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
		 */
		// $twig->addExtension( new Twig\Extension\StringLoaderExtension() );

		$twig->addFilter( new Twig\TwigFilter( 'myfoo', [ $this, 'myfoo' ] ) );

		return $twig;
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
	 *
	 * \@param array $options An array of environment options.
	 *
	 * @return array
	 */
	function update_twig_environment_options( $options ) {
	    // $options['autoescape'] = true;

	    return $options;
	}

	public function assets() {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );

		// Scripts

		wp_enqueue_script( 'gigaverse-main', get_theme_file_uri( '/public/js/main.js' ), [ 'jquery', ], false, true );

		// Styles
		wp_enqueue_style( 'gigaverse-app', get_theme_file_uri( '/public/css/app.css' ) );
	}

	public function disable_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}

	public function remove_menus() {
		remove_menu_page( 'edit-comments.php' );
	}

	public function beep_admin_logo() {
		echo '<style>
		#wpadminbar #wp-admin-bar-wp-logo > .ab-item{
			background-color:#3aa1fa;
			background-image: url(' . get_theme_file_uri('/static/logo.svg') . ') !important;
			background-size: 30px;
			background-repeat: no-repeat;
			background-position: center;
		}
		#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
			color:rgba(0, 0, 0, 0);
		}
	</style>';
	}

	public function disable_feeds() {
		wp_redirect( home_url(), 301 );
		die;
	}
}
