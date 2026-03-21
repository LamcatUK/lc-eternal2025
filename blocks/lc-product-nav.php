<?php
/**
 * Block template for LC Product Nav.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

// Get product categories for navigation cards.
$product_categories = get_terms(
	array(
		'taxonomy'   => 'product_category',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

?>
<section class="product-nav">
	<div class="container py-5">
		<h2 class="has-white-color mb-4">Our products</h2>
		<nav class="product-nav__inner" aria-label="Product Navigation">
			<div class="row">
				<?php
				foreach ( $product_categories as $product_category ) {
					$category_link  = get_term_link( $product_category );
					$category_image = get_field( 'featured_image', $product_category );

					if ( is_wp_error( $category_link ) ) {
						continue;
					}

					?>
				<div class="col-md-4">
					<a class="product-nav__link" href="<?php echo esc_url( $category_link ); ?>">
						<?php if ( ! empty( $category_image['ID'] ) ) : ?>
							<?= wp_get_attachment_image( $category_image['ID'], 'large' ); ?>
						<?php else : ?>
							<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" alt="<?= esc_attr( $product_category->name ); ?>">
						<?php endif; ?>
						<div class="product-nav__overlay"></div>
						<h3><?php echo esc_html( $product_category->name ); ?></h3>
					</a>
				</div>
					<?php
				}
				?>
			</div>
		</nav>
	</div>
</section>