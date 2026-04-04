<?php
/**
 * Block template for LC Contact.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $block['anchor'] ) ) {
	$anchor = 'id="' . esc_attr( $block['anchor'] ) . '"';
}

?>
<div class="container py-5" <?= $anchor; ?>">
	<div class="row g-5">
		<div class="col-md-6" data-aos="fade-right">
			<h2>Contact Us</h2>
			<?= apply_filters( 'the_content', get_field( 'intro' ) ); ?>
			<ul class="fa-ul">
				<li class="mb-3"><span class="fa-li"><i class="fas fa-map-marker-alt"></i></span> <?= wp_kses_post( get_field( 'contact_address', 'option' ) ); ?></li>
				<li class="mb-3"><span class="fa-li"><i class="fas fa-phone"></i></span> <?= do_shortcode( '[contact_phone]' ); ?></li>
				<li class="mb-3"><span class="fa-li"><i class="fas fa-envelope"></i></span> <?= do_shortcode( '[contact_email]' ); ?></li>
			</ul>
		</div>
		<div class="col-md-6" data-aos="fade-left">
			<?= do_shortcode( '[contact-form-7 id="' . esc_attr( get_field( 'form_id' ) ) . '"]' ); ?>
		</div>
	</div>
</div>