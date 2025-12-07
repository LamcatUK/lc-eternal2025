<?php
/**
 * LC Theme Functions
 *
 * This file contains theme-specific functions and customizations for the LC Iology 2025 theme.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

require_once LC_THEME_DIR . '/inc/lc-utility.php';
require_once LC_THEME_DIR . '/inc/lc-block-usage.php';
require_once LC_THEME_DIR . '/inc/lc-acf-theme-palette.php';
require_once LC_THEME_DIR . '/inc/lc-schema.php';
require_once LC_THEME_DIR . '/inc/lc-blocks.php';
require_once LC_THEME_DIR . '/inc/lc-news.php';

/**
 * Editor styles: opt-in so WP loads editor.css in the block editor.
 * With theme.json present, this just adds your custom CSS on top (variables, helpers).
 */
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'css/custom-editor-style.min.css' );
	},
	5
);

/**
 * Neutralise legacy palette/font-size support (if parent/Understrap adds them).
 * theme.json is authoritative, but some themes still register supports in PHP.
 * Remove them AFTER the parent has added them (high priority).
 */
add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'editor-color-palette' );
		remove_theme_support( 'editor-gradient-presets' );
		remove_theme_support( 'editor-font-sizes' );
	},
	99
);

/**
 * (Optional) Ensure custom colours *aren't* forcibly disabled by parent.
 * If Understrap disables custom colours, this re-enables them so theme.json works fully.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' ); // performance nicety.


/**
 * Removes specific page templates from the available templates list.
 *
 * @param array $page_templates The list of page templates.
 * @return array The modified list of page templates.
 */
function child_theme_remove_page_template( $page_templates ) {
	unset(
		$page_templates['page-templates/blank.php'],
		$page_templates['page-templates/empty.php'],
		$page_templates['page-templates/left-sidebarpage.php'],
		$page_templates['page-templates/right-sidebarpage.php'],
		$page_templates['page-templates/both-sidebarspage.php']
	);
	return $page_templates;
}
add_filter( 'theme_page_templates', 'child_theme_remove_page_template' );

/**
 * Remove Understrap post formats support.
 *
 * @return void
 */
function remove_understrap_post_formats() {
	remove_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
add_action( 'after_setup_theme', 'remove_understrap_post_formats', 11 );

/**
 * Add ACF options page for site-wide settings.
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => 'Site-Wide Settings',
			'menu_title' => 'Site-Wide Settings',
			'menu_slug'  => 'theme-general-settings',
			'capability' => 'edit_posts',
		)
	);
}

/**
 * Initialize widgets and navigation menus.
 *
 * Registers custom navigation menus, unregisters default Understrap sidebars and menus,
 * and configures the editor color palette.
 *
 * @return void
 */
function widgets_init() {

	register_nav_menus(
		array(
			'primary_nav'  => 'Primary Nav',
			'footer_menu1' => 'Footer Menu 1',
			'footer_menu2' => 'Footer Menu 2',
		)
	);

	unregister_sidebar( 'hero' );
	unregister_sidebar( 'herocanvas' );
	unregister_sidebar( 'statichero' );
	unregister_sidebar( 'left-sidebar' );
	unregister_sidebar( 'right-sidebar' );
	unregister_sidebar( 'footerfull' );
	unregister_nav_menu( 'primary' );

	add_theme_support( 'disable-custom-colors' );
}
add_action( 'widgets_init', 'widgets_init', 11 );

/**
 * Enqueues theme-specific scripts and styles.
 *
 * This function deregisters jQuery and disables certain styles and scripts
 * that are commented out for potential use in the theme.
 */
function lc_theme_enqueue() {
	$the_theme = wp_get_theme();
	// phpcs:disable
	// wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
	// wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', array(), null, true);
	// wp_enqueue_script('parallax', get_stylesheet_directory_uri() . '/js/parallax.min.js', array('jquery'), null, true);
	// wp_enqueue_style( 'splide-stylesheet', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/css/splide.min.css', array(), null );
	// wp_enqueue_script( 'splide-scripts', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js', array(), null, true );
	// wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
	// wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
	// wp_enqueue_style( 'glightbox-style', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css', array(), $the_theme->get( 'Version' ) );
	// wp_enqueue_script( 'glightbox', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/js/glightbox.min.js', array(), $the_theme->get( 'Version' ), true );
	// phpcs:enable

	wp_deregister_script( 'jquery' );

	wp_enqueue_style( 'splide-stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/css/splide.min.css', array(), null );
	wp_enqueue_script( 'splide-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/js/splide.min.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'lc_theme_enqueue' );

// Performance: Remove WordPress global styles and SVG filters (WP 6.0+).
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

/*------------ LOGIN BITS -----*/

/**
 * Custom login logo.
 */
function wpb_login_logo() {
	?>
<style type="text/css">
	#login h1 a,
	.login h1 a {
		background-image: url(<?php echo esc_url( get_stylesheet_directory_uri() . '/img/iology-logo--colour.svg' ); ?>);
		height: 64px;
		width: 180px;
		background-size: contain;
		background-repeat: no-repeat;
		padding-bottom: 10px;
	}
</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'wpb_login_logo' );

/**
 * Change login logo URL to home URL.
 */
function wpb_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'wpb_login_logo_url' );

/**
 * Change login logo title.
 */
function wpb_login_logo_url_title() {
	return 'Your Site Name and Info';
}
add_filter( 'login_headertitle', 'wpb_login_logo_url_title' );

/**
 * Disable language dropdown on login.
 */
add_filter( 'login_display_language_dropdown', '__return_false' );

/**
 * Disable the emojis.
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins Tinymce plugins.
 * @return array Difference between the two arrays.
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array  $urls        URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference between the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls          = array_diff( $urls, array( $emoji_svg_url ) );
	}
	return $urls;
}

/**
 * Remove jQuery migrate.
 *
 * @param object $scripts WP_Scripts object.
 */
function remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );

/**
 * Remove wp-embed.min.js from footer.
 */
function deregister_wp_embed() {
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'deregister_wp_embed' );


add_action(
	'admin_head',
	function () {
		echo '<style>
		.block-editor-page #wpwrap {
		overflow-y: auto !important;
		}
   </style>';
	}
);

/**
 * WhatsApp button shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string WhatsApp button HTML.
 */
function whatsapp( $atts ) {
	ob_start();

	$default = array(
		'message' => "Hi, I'm contacting you from the iology website.",
		'label'   => 'WhatsApp',
	);

	$a       = shortcode_atts($default, $atts);
	$message = htmlspecialchars($a['message'], ENT_QUOTES);
	$label   = $a['label'];

	$phone = parse_phone(get_field('mobile', 'options'));

	?>
<a href="https://api.whatsapp.com/send?phone=<?= $phone; ?>&text=<?= $message; ?>"
	class="btn btn-primary me-2 mb-2" target="_blank"><?= $label; ?></a>
	<?php

	return ob_get_clean();
}
add_shortcode( 'whatsapp_button', 'whatsapp' );
