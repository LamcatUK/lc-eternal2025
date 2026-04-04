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

$title   = get_field( 'title' );
$content = get_field( 'intro' );

?>
<section class="product-nav lc-nav-cards">
	<div class="container py-5">
		<?php if ( $title ) : ?>
			<h2 class="has-white-color mb-4"><?= esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $content ) : ?>
			<div class="has-white-color larger mb-5"><?= wp_kses_post( wpautop( $content ) ); ?></div>
		<?php endif; ?>
		<nav class="product-nav__inner" aria-label="Product Navigation">
			<div class="row g-4">
				<?php
				foreach ( $product_categories as $product_category ) {
					$category_link  = get_term_link( $product_category );
					$category_image = get_field( 'featured_image', $product_category );

					if ( is_wp_error( $category_link ) ) {
						continue;
					}

					?>
				<div class="col-md-3">
					<a class="lc-nav-cards__link" href="<?php echo esc_url( $category_link ); ?>">
						<div class="lc-nav-cards__media">
							<?php if ( ! empty( $category_image['ID'] ) ) : ?>
								<?= wp_get_attachment_image( $category_image['ID'], 'large' ); ?>
							<?php else : ?>
								<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" alt="<?= esc_attr( $product_category->name ); ?>">
							<?php endif; ?>
							<div class="lc-nav-cards__overlay"></div>
						</div>
						<div class="lc-nav-cards__title-wrap">
							<h3><?php echo esc_html( $product_category->name ); ?></h3>
						</div>
					</a>
				</div>
					<?php
				}
				?>
			</div>
		</nav>
	</div>
</section>
