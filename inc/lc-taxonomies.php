<?php
/**
 * File: lc-taxonomies.php
 * Description: Registers custom taxonomies for the theme.
 *
 * @package lc-eternal2025
 */

/**
 * Registers custom ACF blocks for the theme.
 *
 * This function is used to define and register Advanced Custom Fields (ACF) blocks
 * that can be used within the WordPress block editor. Each block can have its own
 * settings, templates, and styles.
 *
 * @return void
 */
function lc_register_taxonomies() {
	register_taxonomy(
		'product_category',
		'product',
		array(
			'label'             => 'Product Categories',
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'products',
				'with_front' => false,
			),
		)
	);
}

add_action( 'init', 'lc_register_taxonomies' );
