<?php
/**
 * Block template for LC Products by Category.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$category_id = get_field( 'category' );

if ( ! $category_id ) {
	return;
}

$category = get_term( $category_id, 'product_category' );

if ( ! $category || is_wp_error( $category ) ) {
	return;
}

$products = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_category',
				'field'    => 'term_id',
				'terms'    => $category_id,
			),
		),
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

?>
<section class="products-by-category py-5">
	<div class="container">
		<?php if ( $category->description ) : ?>
			<div class="category-description mb-4">
				<?= wp_kses_post( wpautop( $category->description ) ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( $products->have_posts() ) : ?>
			<div class="products-grid">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th style="width:66px;"></th>
							<th>Product</th>
							<th>Description</th>
							<th>Material</th>
							<th>Size</th>
							<th>Colour</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
						<?php
						while ( $products->have_posts() ) {
							$products->the_post();
							$product_id   = get_the_ID();
							$sku          = get_the_title();
							$product_name = lc_get_product_display_name( $product_id );
							$description  = get_field( 'description', $product_id );
							$material     = get_field( 'material', $product_id );
							$size         = get_field( 'size', $product_id );
							$colour       = get_field( 'colour', $product_id );
							?>
							<tr>
								<td>
									<?php if ( has_post_thumbnail() ) : ?>
										<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'thumbnail' ) ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="<?= esc_attr( $product_name ); ?>">
									<?php else : ?>
										<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="">
									<?php endif; ?>
								</td>
								<td>
									<strong><?= esc_html( $product_name ); ?></strong>
									<?php if ( $product_name !== $sku ) : ?>
										<div class="small text-muted"><?= esc_html( $sku ); ?></div>
									<?php endif; ?>
								</td>
								<td><?= wp_kses_post( wp_trim_words( $description, 15 ) ); ?></td>
								<td><?= esc_html( $material ? $material : '-' ); ?></td>
								<td><?= esc_html( $size ? $size : '-' ); ?></td>
								<td><?= esc_html( $colour ? $colour : '-' ); ?></td>
								<td>
									<a href="<?= esc_url( get_permalink() ); ?>" class="btn btn-sm btn-outline-primary">
										View Details
									</a>
								</td>
							</tr>
							<?php
						}
						wp_reset_postdata();
						?>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<p>No products found in this category.</p>
		<?php endif; ?>
	</div>
</section>
