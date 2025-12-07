<?php
/**
 * Block template for LC Service Card Slider.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$services = get_field( 'services' );

if ( empty( $services ) ) {
	return;
}

?>
<section class="three_cards service_card_slider py-5">
	<div class="container-xl">	
		<div class="splide service-slider">
			<div class="splide__track" style="padding: 1rem 0; margin: -1rem 0;">
				<ul class="splide__list">
					<?php
					foreach ( $services as $service_key ) {
						$icon        = wp_get_attachment_image_url( get_field( $service_key . '_icon', 'options' ), 'large' );
						$description = get_field( $service_key . '_description', 'options' );
						$link        = get_field( $service_key . '_link', 'options' );

						if ( ! $link ) {
							continue;
						}

						// Get the label from ACF field choices.
						$field         = get_field_object( 'services' );
						$service_label = $field['choices'][ $service_key ] ?? ucwords( str_replace( '_', ' ', $service_key ) );
						?>
					<li class="splide__slide">
						<div class="card">
							<?php if ( $icon ) { ?>
							<div class="card__icon">
								<img src="<?= esc_url( $icon ); ?>" alt="<?= esc_attr( $service_label ); ?>">
							</div>
							<?php } ?>
							<div class="card__bottom">
								<h3><?= esc_html( $service_label ); ?></h3>
								<?php if ( $description ) { ?>
								<p><?= esc_html( $description ); ?></p>
								<?php } ?>
							</div>
							<div class="card__button">
								<a href="<?= esc_url( $link['url'] ); ?>"
									target="<?= esc_attr( $link['target'] ?? '_self' ); ?>"
									class="btn btn-primary">Learn More</a>
							</div>
						</div>
					</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</section>
<?php
add_action(
	'wp_footer',
	function () {
		?>
<script>
new Splide('.service-slider', {
	type: 'loop',
	perPage: 2,
	perMove: 1,
	gap: '1rem',
	autoplay: true,
	interval: 4000,
	arrows: false,
	pagination: false,
	breakpoints: {
		768: {
			perPage: 1,
		}
	}
}).mount();
</script>
		<?php
	},
	999
);