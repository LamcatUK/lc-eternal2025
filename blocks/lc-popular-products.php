<?php
/**
 * Block template for LC Popular Products.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$products = get_field( 'products' );

if ( empty( $products ) || ! is_array( $products ) ) {
	return;
}

$title              = get_field( 'title' );
$content            = get_field( 'content' );

$class_name         = $block['className'] ?? '';
$anchor             = $block['anchor'] ?? '';
$block_id           = 'lc-popular-products-' . $block['id'];
$archive_link       = get_post_type_archive_link( 'product' );
$section_attributes = 'class="lc-popular-products py-5 ' . esc_attr( $class_name ) . '"';

if ( $anchor ) {
	$section_attributes .= ' id="' . esc_attr( $anchor ) . '"';
}
?>
<section <?= $section_attributes; ?>>
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class=""><?= esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<div class="larger mb-5"><?= wp_kses_post( wpautop( $content ) ); ?></div>
		<?php endif; ?>

		<div class="splide lc-popular-products__slider mb-4" id="<?= esc_attr( $block_id ); ?>">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ( $products as $product_id ) : ?>
						<?php
						$sku          = get_the_title( $product_id );
						$product_name = lc_get_product_display_name( $product_id );
						$material     = get_field( 'material', $product_id );
						$size         = trim( get_field( 'size', $product_id ) . ' ' . get_field( 'size_units', $product_id ) );
						$colour       = get_field( 'colour', $product_id );
						$details      = array_filter(
							array(
								$material,
								$size,
								$colour,
							)
						);
						?>
						<li class="splide__slide">
							<a class="lc-product-card lc-popular-products__card" href="<?= esc_url( get_permalink( $product_id ) ); ?>">
								<div class="lc-product-card__image">
									<?php if ( has_post_thumbnail( $product_id ) ) : ?>
										<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'medium' ) ); ?>" alt="<?= esc_attr( $product_name ); ?>">
									<?php else : ?>
										<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" alt="<?= esc_attr( $product_name ); ?>">
									<?php endif; ?>
								</div>

								<div class="lc-product-card__body">
									<h3 class="lc-product-card__title"><?= esc_html( $product_name ); ?></h3>
									<p class="lc-product-card__sku"><?= esc_html( $sku ); ?></p>
									<?php if ( ! empty( $details ) ) : ?>
										<p class="lc-product-card__meta text-muted small lc-popular-products__meta"><?= esc_html( implode( ' • ', $details ) ); ?></p>
									<?php endif; ?>
								</div>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<?php if ( $archive_link ) : ?>
			<div class="text-center">
				<a href="<?= esc_url( $archive_link ); ?>" class="ep-button ep-button--primary">All products</a>
			</div>
		<?php endif; ?>
	</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
	if (!window.Splide) {
		return;
	}

	var popularProductsSlider = document.getElementById(<?= wp_json_encode( $block_id ); ?>);

	if (!popularProductsSlider || popularProductsSlider.dataset.splideMounted === 'true') {
		return;
	}

	new Splide(popularProductsSlider, {
		type: 'loop',
		perPage: 4,
		perMove: 1,
		gap: '1.5rem',
		autoplay: true,
		pagination: false,
		interval: 3000,
		breakpoints: {
			1200: { perPage: 3 },
			768: { perPage: 2 },
			576: { perPage: 1 }
		}
	}).mount();

	popularProductsSlider.dataset.splideMounted = 'true';
});
</script>
