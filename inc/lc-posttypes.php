<?php
/**
 * Custom Post Types Registration
 *
 * This file contains the code to register custom post types
 * for the theme.
 *
 * @package lc-eternal2025
 */

/**
 * Registers custom post types for the theme.
 *
 * This function defines and registers the "Testimonials" post type
 * with their respective labels, arguments, and settings.
 */
function lc_register_post_types() {

	register_post_type(
		'product',
		array(
			'labels'              => array(
				'name'          => 'Products',
				'singular_name' => 'Product',
			),
			'public'              => true,
			'has_archive'         => true,
			'rewrite'             => array(
				'slug'       => 'product',
				'with_front' => false,
			),
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'thumbnail' ),
			'menu_icon'           => 'dashicons-cart',
			'show_in_graphql'     => true,
			'graphql_single_name' => 'Product',
			'graphql_plural_name' => 'Products',
		)
	);
}

add_action( 'init', 'lc_register_post_types' );

/**
 * Flushes rewrite rules after registering custom post types.
 *
 * This function ensures that the rewrite rules are properly flushed
 * whenever the theme is switched, so that the custom post types
 * are correctly registered and accessible.
 */
function lc_rewrite_flush() {
	lc_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'lc_rewrite_flush' );
