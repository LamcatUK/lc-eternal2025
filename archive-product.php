<?php
/**
 * Template: archive-product.php
 * Description: All Products grid with filtering using ACF fields and taxonomies
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$product_categories = get_terms(
	array(
		'taxonomy'   => 'product_category',
		'hide_empty' => true,
	)
);
?>
<main id="main">

	<!-- Hero -->
	<section class="hero hero--short">
		<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
			<div class="carousel-inner">
				<div class="carousel-item active">
					<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-hero.jpg' ); ?>" class="d-block w-100 h-100" alt="Products">
				</div>
			</div>
		</div>
		<div class="hero__overlay"></div>
		<div class="hero__content d-flex align-items-center">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 text-white">
						<h1 class="hero__title">Products</h1>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="products-by-category py-5">
		<div class="container">

			<!-- Filters -->
			<div class="row g-3 mb-4 align-items-end">
				<div class="col-md-4">
					<label for="skuSearch" class="form-label">Search by Product Code</label>
					<input type="text" id="skuSearch" class="form-control" placeholder="e.g. ABC123" aria-label="Search by Product Code">
				</div>
				<div class="col-md-4">
					<label for="categoryFilter" class="form-label">Category</label>
					<select id="categoryFilter" class="form-select">
						<option value="">All Categories</option>
						<?php foreach ( $product_categories as $product_cat ) : ?>
							<option value="<?= esc_attr( $product_cat->slug ); ?>"><?= esc_html( $product_cat->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-4 d-grid">
					<label class="form-label invisible">Reset</label>
					<button id="resetFilters" class="btn btn-secondary">Reset Filters</button>
				</div>
			</div>

			<p id="productCount" class="fw-bold mb-3">&nbsp;</p>

			<!-- Products Table -->
			<div class="products-grid">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th style="width:66px;"></th>
							<th>Product Code</th>
							<th>Description</th>
							<th>Category</th>
							<th>Material</th>
							<th>Size</th>
							<th>Colour</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody id="productGrid">
						<?php
						while ( have_posts() ) {
							the_post();
							$product_id     = get_the_ID();
							$sku            = get_the_title();
							$description    = get_field( 'description', $product_id );
							$material       = get_field( 'material', $product_id );
							$size           = get_field( 'size', $product_id );
							$colour         = get_field( 'colour', $product_id );
							$category_terms = wp_get_post_terms( $product_id, 'product_category' );
							$cat_slugs      = wp_list_pluck( $category_terms, 'slug' );
							$cat_names      = wp_list_pluck( $category_terms, 'name' );
							?>
							<tr class="product-card"
								data-sku="<?= esc_attr( $sku ); ?>"
								data-category="<?= esc_attr( implode( ',', $cat_slugs ) ); ?>">
								<td>
									<?php if ( has_post_thumbnail() ) : ?>
										<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'thumbnail' ) ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="<?= esc_attr( $sku ); ?>">
									<?php else : ?>
										<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="">
									<?php endif; ?>
								</td>
								<td><strong><?= esc_html( $sku ); ?></strong></td>
								<td><?= wp_kses_post( wp_trim_words( $description, 15 ) ); ?></td>
								<td><?= esc_html( ! empty( $cat_names ) ? implode( ', ', $cat_names ) : '-' ); ?></td>
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
						?>
					</tbody>
				</table>
			</div>

		</div>
	</section>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
	const params = new URLSearchParams(window.location.search);
	document.getElementById('skuSearch').value = params.get('sku') || '';
	document.getElementById('categoryFilter').value = params.get('category') || '';

	document.getElementById('skuSearch').addEventListener('input', filterCards);
	document.getElementById('categoryFilter').addEventListener('change', filterCards);
	document.getElementById('resetFilters').addEventListener('click', () => {
		document.getElementById('skuSearch').value = '';
		document.getElementById('categoryFilter').value = '';
		filterCards();
	});

	filterCards();
});

function filterCards() {
	let visibleCount = 0;
	const skuQuery = document.getElementById('skuSearch').value.toLowerCase();
	const selectedCategory = document.getElementById('categoryFilter').value;

	const cards = document.querySelectorAll('.product-card');
	cards.forEach(card => {
		const sku = card.dataset.sku.toLowerCase();
		const category = card.dataset.category.split(',').filter(c => c.trim());

		const matchesSku = sku.includes(skuQuery);
		const matchesCategory = !selectedCategory || category.includes(selectedCategory);

		const visible = matchesSku && matchesCategory;
		card.style.display = visible ? '' : 'none';
		visibleCount += visible ? 1 : 0;
	});

	document.getElementById('productCount').textContent = `${visibleCount} product${visibleCount !== 1 ? 's' : ''} found`;
}
</script>

<?php get_footer(); ?>
