<?php
/**
 * Template: taxonomy-product_category.php
 * Description: Archive template for product_category taxonomy terms.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term = get_queried_object();

$products = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_category',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
?>
<main id="main">

	<!-- Hero -->
	<section class="hero hero--short">
		<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
			<div class="carousel-inner">
				<div class="carousel-item active">
					<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-hero.jpg' ); ?>" class="d-block w-100 h-100" alt="<?= esc_attr( $term->name ); ?>">
				</div>
			</div>
		</div>
		<div class="hero__overlay"></div>
		<div class="hero__content d-flex align-items-center">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 text-white">
						<h1 class="hero__title"><?= esc_html( $term->name ); ?></h1>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Products -->
	<section class="products-by-category py-5">
		<div class="container">

			<?php if ( $term->description ) : ?>
				<div class="category-description mb-4">
					<?= wp_kses_post( wpautop( $term->description ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $products->have_posts() ) : ?>
				<div class="row g-3 mb-4 align-items-end">
					<div class="col-md-4">
						<label for="skuSearch" class="form-label">Search by Product Code</label>
						<input type="text" id="skuSearch" class="form-control" placeholder="e.g. ABC123" aria-label="Search by Product Code">
					</div>
					<div class="col-md-2 d-grid">
						<label class="form-label invisible">Reset</label>
						<button id="resetFilters" class="btn btn-secondary">Reset</button>
					</div>
				</div>

				<p id="productCount" class="fw-bold mb-3">&nbsp;</p>

				<div class="products-grid">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width:66px;"></th>
								<th>Product Code</th>
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
								$product_id  = get_the_ID();
								$description = get_field( 'description', $product_id );
								$material    = get_field( 'material', $product_id );
								$size        = get_field( 'size', $product_id );
								$colour      = get_field( 'colour', $product_id );
								?>
								<tr class="product-card" data-sku="<?= esc_attr( get_the_title() ); ?>">
									<td>
										<?php if ( has_post_thumbnail() ) : ?>
											<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'thumbnail' ) ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="<?= esc_attr( get_the_title() ); ?>">
										<?php else : ?>
											<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="">
										<?php endif; ?>
									</td>
									<td><strong><?= esc_html( get_the_title() ); ?></strong></td>
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

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
	document.getElementById('skuSearch').addEventListener('input', filterCards);
	document.getElementById('resetFilters').addEventListener('click', () => {
		document.getElementById('skuSearch').value = '';
		filterCards();
	});
	filterCards();
});

function filterCards() {
	let visibleCount = 0;
	const skuQuery = document.getElementById('skuSearch').value.toLowerCase();

	document.querySelectorAll('.product-card').forEach(row => {
		const visible = row.dataset.sku.toLowerCase().includes(skuQuery);
		row.style.display = visible ? '' : 'none';
		visibleCount += visible ? 1 : 0;
	});

	document.getElementById('productCount').textContent = `${visibleCount} product${visibleCount !== 1 ? 's' : ''} found`;
}
</script>

<?php get_footer(); ?>
