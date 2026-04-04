<?php
/**
 * Block template for LC Wide Map.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$maps_url = get_field( 'maps_url', 'option' );
?>
<iframe src="<?= esc_url( $maps_url ); ?>" width="100%" height="400" style="border:0;display:block" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>