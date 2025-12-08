<?php
/**
 * Block template for LC Product Nav.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

// get children of page '/products/'.
$product_pages = get_pages(
	array(
		'child_of'    => get_page_by_path( 'products' )->ID,
		'sort_column' => 'menu_order',
		'sort_order'  => 'ASC',
	)
);

?>
<section class="product-nav">
	<div class="container py-5">
		<h2 class="has-white-color mb-4">Our products</h2>
		<nav class="product-nav__inner" aria-label="Product Navigation">
			<div class="row">
				<?php
				foreach ( $product_pages as $page ) {
					?>
				<div class="col-md-4">
					<a class="product-nav__link" href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>">
						<?= get_the_post_thumbnail( $page->ID, 'large'); ?>
						<div class="product-nav__overlay"></div>
						<h3><?php echo esc_html( get_the_title( $page->ID ) ); ?></h3>
					</a>
				</div>
					<?php
				}
				?>
			</div>
		</nav>
	</div>
</section>