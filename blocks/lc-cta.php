<?php
/**
 * Block template for LC CTA.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$cta_title   = get_field( 'cta_title', 'option' ) ? get_field( 'cta_title', 'option' ) : 'Let’s Talk Packaging?';
$cta_content = get_field( 'cta_content', 'option' ) ? get_field( 'cta_content', 'option' ) : 'Need standard products or custom solutions? We’re here to help. Quickly, reliably, and to spec.';
$cta_link    = get_field( 'cta_link', 'option' ) ? get_field( 'cta_link', 'option' ) : array();

$phone = get_field( 'contact_phone', 'option' );

$bg = get_field( 'cta_background', 'option' );
?>
<section class="section-cta-banner">
	<?php
	if ( $bg ) {
		echo wp_get_attachment_image( $bg, 'full', false, array( 'class' => 'section-cta-banner__background' ) );
	} else {
		?>
		<div class="section-cta-banner__background" style="background-color: red;"></div>
		<?php
	}
	?>
	<div class="section-cta-banner__overlay"></div>
	<div class="container" data-aos="fade">
		<div class="inner">
			<h2><?= esc_html( $cta_title ); ?></h2>
			<p><?= esc_html( $cta_content ); ?></p>
			<div class="mb-4">
				<a href="<?= esc_url( $cta_link['url'] ); ?>"
				target="<?= esc_attr( $cta_link['target'] ); ?>"
				class="ep-button ep-button--primary"><?= esc_html( $cta_link['title'] ); ?></a>
			</div>
			<?= do_shortcode( '[contact_phone text="Call Us on ' . $phone . '" class="phone-link"]' ); ?>
		</div>
	</div>
</section>
