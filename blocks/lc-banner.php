<?php
/**
 * Banner Block Template.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- banner -->
<section class="banner py-1">
	<div class="container-xl text-center d-md-flex justify-content-center align-items-center">
		<?php
		if ( get_field( 'icon' ) ) {
			$icon      = wp_get_attachment_image_url( get_field( 'icon' ), 'full' );
			$image_alt = get_post_meta( get_field( 'icon' ), '_wp_attachment_image_alt', true );
			?>
		<img class="banner__icon me-4" src="<?= esc_url( $icon ); ?>"
			alt="<?= esc_attr( $image_alt ); ?>">
			<?php
		}
		?>
		<div class="banner__content">
			<?= esc_html( get_field( 'content' ) ); ?></div>
	</div>
</section>
