<?php
/**
 * Disable Blog Functionality
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove blog-related menus from admin.
 *
 * @return void
 */
function lc_remove_menus() {
	remove_menu_page( 'edit.php' ); // Hides the 'Posts' menu.
}
add_action( 'admin_menu', 'lc_remove_menus' );

/**
 * Remove post-related capabilities from editor role.
 *
 * @return void
 */
function lc_remove_post_capabilities() {
	$role = get_role( 'editor' ); // or another role as necessary.
	$role->remove_cap( 'edit_posts' );
	$role->remove_cap( 'publish_posts' );
	$role->remove_cap( 'delete_posts' );
}
add_action( 'init', 'lc_remove_post_capabilities' );
