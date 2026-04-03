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
			<div class="row g-3 mb-4 align-items-end product-browser__toolbar">
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
				<div class="col-md-2 d-grid">
					<label class="form-label invisible">Reset</label>
					<button id="resetFilters" class="btn btn-primary">Reset Filters</button>
				</div>
				<div class="col-2">
					<div class="product-browser__switcher" role="group" aria-label="Product view switcher">
						<button type="button" class="btn btn-outline-primary is-active" data-view="grid"><i class="fa-solid fa-grip"></i></button>
						<button type="button" class="btn btn-outline-primary" data-view="row"><i class="fa-solid fa-bars"></i></button>
					</div>
				</div>
			</div>

			<p id="productCount" class="fw-bold mb-3">&nbsp;</p>

			<div class="product-browser product-browser--archive" data-default-view="grid">
				<div class="products-grid-view">
					<div id="productGridCards" class="row g-4">
						<?php
						while ( have_posts() ) {
							the_post();
							$product_id     = get_the_ID();
							$sku            = get_the_title();
							$product_name   = lc_get_product_display_name( $product_id );
							$material       = get_field( 'material', $product_id );
							$size           = get_field( 'size', $product_id );
							$size_units     = get_field( 'size_units', $product_id );
							$colour         = get_field( 'colour', $product_id );
							$category_terms = wp_get_post_terms( $product_id, 'product_category' );
							$cat_slugs      = wp_list_pluck( $category_terms, 'slug' );
							$cat_names      = wp_list_pluck( $category_terms, 'name' );
							$size_label     = trim( $size . ' ' . $size_units );
							?>
							<div class="col-md-6 col-xl-3 product-browser__item product-browser__item--grid" data-sku="<?= esc_attr( $sku ); ?>" data-category="<?= esc_attr( implode( ',', $cat_slugs ) ); ?>">
								<a href="<?= esc_url( get_permalink() ); ?>" class="product-browser__card text-decoration-none">
									<div class="product-browser__card-header">
										<h2><?= esc_html( $product_name ); ?></h2>
									</div>
									<div class="product-browser__card-body">
										<div class="product-browser__card-layout">
											<div class="product-browser__card-image">
												<?php if ( has_post_thumbnail() ) : ?>
													<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'medium' ) ); ?>" alt="<?= esc_attr( $product_name ); ?>">
												<?php else : ?>
													<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" alt="<?= esc_attr( $product_name ); ?>">
												<?php endif; ?>
											</div>
											<div class="product-browser__card-meta small">
												<div><strong>Size:</strong> <?= esc_html( $size_label ? $size_label : '-' ); ?></div>
												<div><strong>Material:</strong> <?= esc_html( $material ? $material : '-' ); ?></div>
												<?php if ( $colour ) : ?>
													<div><strong>Colour:</strong> <?= esc_html( $colour ); ?></div>
												<?php endif; ?>
												<div><strong>Type:</strong> <?= esc_html( ! empty( $cat_names ) ? implode( ', ', $cat_names ) : '-' ); ?></div>
												<div class="product-browser__sku text-muted"><?= esc_html( $sku ); ?></div>
											</div>
										</div>
									</div>
								</a>
							</div>
							<?php
						}
						wp_reset_postdata();
						?>
					</div>
				</div>

				<!-- Products Table -->
				<div class="products-grid products-row-view d-none">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th style="width:66px;"></th>
							<th>Product Code</th>
							<th>Product Name</th>
							<th>Material</th>
							<th>Size</th>
							<th>Category</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody id="productGrid">
						<?php
						rewind_posts();
						while ( have_posts() ) {
							the_post();
							$product_id     = get_the_ID();
							$sku            = get_the_title();
							$product_name   = lc_get_product_display_name( $product_id );
							$material       = get_field( 'material', $product_id );
							$size           = get_field( 'size', $product_id );
							$size_units     = get_field( 'size_units', $product_id );
							$category_terms = wp_get_post_terms( $product_id, 'product_category' );
							$cat_slugs      = wp_list_pluck( $category_terms, 'slug' );
							$cat_names      = wp_list_pluck( $category_terms, 'name' );
							$size_label     = trim( $size . ' ' . $size_units );
							?>
							<tr class="product-card"
								data-sku="<?= esc_attr( $sku ); ?>"
								data-category="<?= esc_attr( implode( ',', $cat_slugs ) ); ?>">
								<td>
									<?php if ( has_post_thumbnail() ) : ?>
										<img src="<?= esc_url( get_the_post_thumbnail_url( $product_id, 'thumbnail' ) ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="<?= esc_attr( $product_name ); ?>">
									<?php else : ?>
										<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" width="50" height="50" style="object-fit:cover;border-radius:4px;" alt="">
									<?php endif; ?>
								</td>
								<td><strong><?= esc_html( $sku ); ?></strong></td>
								<td><?= esc_html( $product_name ); ?></td>
								<td><?= esc_html( $material ? $material : '-' ); ?></td>
								<td><?= esc_html( $size_label ? $size_label : '-' ); ?></td>
								<td><?= esc_html( ! empty( $cat_names ) ? implode( ', ', $cat_names ) : '-' ); ?></td>
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

		</div>
	</section>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
	const viewButtons = document.querySelectorAll('.product-browser__switcher [data-view]');
	const browser = document.querySelector('.product-browser');
	const gridView = document.querySelector('.products-grid-view');
	const rowView = document.querySelector('.products-row-view');
	const storageKey = 'lcProductBrowserView';
	const params = new URLSearchParams(window.location.search);
	document.getElementById('skuSearch').value = params.get('sku') || '';
	document.getElementById('categoryFilter').value = params.get('category') || '';

	document.getElementById('skuSearch').addEventListener('input', filterCards);
	document.getElementById('categoryFilter').addEventListener('change', filterCards);
	viewButtons.forEach(button => {
		button.addEventListener('click', () => setView(button.dataset.view));
	});
	document.getElementById('resetFilters').addEventListener('click', () => {
		document.getElementById('skuSearch').value = '';
		document.getElementById('categoryFilter').value = '';
		filterCards();
	});

	setView(localStorage.getItem(storageKey) || browser.dataset.defaultView || 'grid');
	filterCards();

	function setView(view) {
		const isGrid = view !== 'row';
		gridView.classList.toggle('d-none', !isGrid);
		rowView.classList.toggle('d-none', isGrid);
		viewButtons.forEach(button => button.classList.toggle('is-active', button.dataset.view === view));
		localStorage.setItem(storageKey, view);
	}
});

function filterCards() {
	let visibleCount = 0;
	const skuQuery = document.getElementById('skuSearch').value.toLowerCase();
	const selectedCategory = document.getElementById('categoryFilter').value;

	const rowItems = document.querySelectorAll('.product-card');
	const gridItems = document.querySelectorAll('.product-browser__item--grid');

	rowItems.forEach((card, index) => {
		const sku = card.dataset.sku.toLowerCase();
		const category = card.dataset.category.split(',').filter(c => c.trim());

		const matchesSku = sku.includes(skuQuery);
		const matchesCategory = !selectedCategory || category.includes(selectedCategory);

		const visible = matchesSku && matchesCategory;
		card.style.display = visible ? '' : 'none';
		if (gridItems[index]) {
			gridItems[index].style.display = visible ? '' : 'none';
		}
		visibleCount += visible ? 1 : 0;
	});

	document.getElementById('productCount').textContent = `${visibleCount} product${visibleCount !== 1 ? 's' : ''} found`;
}
</script>

<?php get_footer(); ?>
