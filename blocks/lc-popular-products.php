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
$background         = get_field( 'background' );
$background_class   = $background ? 'has-' . sanitize_html_class( $background ) . '-background-color' : 'has-white-background-color';
$class_name         = $block['className'] ?? '';
$anchor             = $block['anchor'] ?? '';
$block_id           = 'lc-popular-products-' . $block['id'];
$archive_link       = get_post_type_archive_link( 'product' );
$section_attributes = 'class="lc-popular-products py-5 ' . esc_attr( trim( $background_class . ' ' . $class_name ) ) . '"';

if ( $anchor ) {
	$section_attributes .= ' id="' . esc_attr( $anchor ) . '"';
}
?>
<section <?= $section_attributes; ?>>
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class="text-center"><?= esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<div class="text-center mb-4"><?= wp_kses_post( wpautop( $content ) ); ?></div>
		<?php endif; ?>

		<div class="splide lc-popular-products__slider mb-4" id="<?= esc_attr( $block_id ); ?>">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ( $products as $product_id ) : ?>
						<?php
						$sku          = get_the_title( $product_id );
						$product_name = lc_get_product_display_name( $product_id );
						$description  = get_field( 'description', $product_id );
						$material     = get_field( 'material', $product_id );
						$size         = get_field( 'size', $product_id );
						$size_units   = get_field( 'size_units', $product_id );
						$colour       = get_field( 'colour', $product_id );
						$pack_size    = get_field( 'pack_size', $product_id );
						?>
						<li class="splide__slide">
							<a class="card h-100 lc-popular-products__card" href="<?= esc_url( get_permalink( $product_id ) ); ?>">
								<?php if ( has_post_thumbnail( $product_id ) ) : ?>
									<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'medium' ) ); ?>" class="card-img-top lc-popular-products__image" alt="<?= esc_attr( $product_name ); ?>">
								<?php else : ?>
									<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" class="card-img-top lc-popular-products__image" alt="<?= esc_attr( $product_name ); ?>">
								<?php endif; ?>

								<div class="card-body d-flex flex-column">
									<h3 class="card-title h5 mb-2"><?= esc_html( $product_name ); ?></h3>

									<?php if ( $description ) : ?>
										<p class="card-text mb-3"><?= esc_html( wp_trim_words( wp_strip_all_tags( $description ), 18 ) ); ?></p>
									<?php endif; ?>

									<ul class="list-unstyled mb-0 small lc-popular-products__meta">
										<li><strong>Material:</strong> <?= esc_html( $material ? $material : '-' ); ?></li>
										<li><strong>Size:</strong> <?= esc_html( $size ? trim( $size . ' ' . $size_units ) : '-' ); ?></li>
										<li><strong>Colour:</strong> <?= esc_html( $colour ? $colour : '-' ); ?></li>
										<li><strong>Pack Size:</strong> <?= esc_html( $pack_size ? $pack_size : '-' ); ?></li>
										<li><strong>SKU:</strong> <?= esc_html( $sku ); ?></li>
									</ul>
								</div>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<?php if ( $archive_link ) : ?>
			<div class="text-center">
				<a href="<?= esc_url( $archive_link ); ?>" class="btn btn-primary">All products</a>
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
