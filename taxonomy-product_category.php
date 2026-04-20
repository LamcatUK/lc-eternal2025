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

$term_content = get_field( 'content', $term );
$term_faqs    = get_field( 'faq', $term );

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
				<div class="row align-items-center g-4">
					<div class="col-lg-8 text-white">
						<h1 class="hero__title"><?= esc_html( $term->name ); ?></h1>
					</div>
					<div class="col-lg-4 d-none d-lg-block">
						<?php
						if ( get_field( 'hero_image', $term ) ) {
							echo wp_get_attachment_image( get_field( 'hero_image', $term ), 'full', false, array( 'class' => 'img-fluid rounded' ) );
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php
	if ( $term_content ) {
		?>
		<section class="term-content pt-5">
			<div class="container">
				<?= wp_kses_post( wpautop( $term_content ) ); ?>
			</div>
		</section>
		<?php
	}
	?>

<?php
if ( $products->have_posts() ) {
	?>
	<!-- Products -->
	<section class="products-by-category py-5">
		<div class="container">
			<h2 class="mb-4">Products in <?= esc_html( $term->name ); ?></h2>
		<?php
		if ( $term->description ) {
			?>
				<div class="category-description mb-4">
				<?= wp_kses_post( wpautop( $term->description ) ); ?>
				</div>
				<?php
		}

		?>
				<div class="row g-3 mb-4 align-items-end product-browser__toolbar">
					<div class="col-md-4">
						<label for="skuSearch" class="form-label">Search by Product Code</label>
						<input type="text" id="skuSearch" class="form-control" placeholder="e.g. ABC123" aria-label="Search by Product Code">
					</div>
					<div class="col-md-2 d-grid">
						<label class="form-label invisible">Reset</label>
						<button id="resetFilters" class="btn btn-secondary">Reset</button>
					</div>
					<div class="col-md-2">
						<div class="product-browser__switcher" role="group" aria-label="Product view switcher">
							<button type="button" class="btn btn-outline-primary is-active" data-view="grid"><i class="fa-solid fa-grip"></i></button>
							<button type="button" class="btn btn-outline-primary" data-view="row"><i class="fa-solid fa-bars"></i></button>
						</div>
					</div>
				</div>

				<p id="productCount" class="fw-bold mb-3">&nbsp;</p>

				<div class="product-browser product-browser--taxonomy" data-default-view="grid">
					<div class="products-grid-view">
						<div class="row g-4" id="productGridCards">
						<?php
						while ( $products->have_posts() ) {
							$products->the_post();
							$product_id   = get_the_ID();
							$sku          = get_the_title();
							$product_name = lc_get_product_display_name( $product_id );
							$material     = get_field( 'material', $product_id );
							$size         = get_field( 'size', $product_id );
							$size_units   = get_field( 'size_units', $product_id );
							$colour       = get_field( 'colour', $product_id );
							$size_label   = trim( $size . ' ' . $size_units );
							?>
								<div class="col-md-6 col-xl-3 product-browser__item product-browser__item--grid" data-sku="<?= esc_attr( $sku ); ?>">
									<a href="<?= esc_url( get_permalink() ); ?>" class="product-browser__card text-decoration-none">
										<div class="product-browser__card-header">
											<h3 class="h5 mb-0"><?= esc_html( $product_name ); ?></h3>
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

					<div class="products-grid products-row-view d-none">
					<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th style="width:66px;"></th>
								<th>Product Code</th>
								<th>Product Name</th>
								<th>Material</th>
								<th>Size</th>
								<th>Details</th>
							</tr>
						</thead>
						<tbody>
						<?php
						rewind_posts();
						while ( $products->have_posts() ) {
							$products->the_post();
							$product_id   = get_the_ID();
							$sku          = get_the_title();
							$product_name = lc_get_product_display_name( $product_id );
							$material     = get_field( 'material', $product_id );
							$size         = get_field( 'size', $product_id );
							$size_units   = get_field( 'size_units', $product_id );
							$size_label   = trim( $size . ' ' . $size_units );
							?>
								<tr class="product-card" data-sku="<?= esc_attr( $sku ); ?>">
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
				</div>
				</div>

		</div>
	</section>
		<?php
}
?>
	<!-- FAQ CONTENT HERE -->
	<?php
	if ( ! empty( $term_faqs ) && is_array( $term_faqs ) ) {
		?>
		<section class="faq py-5">
			<div class="container-xl">
				<h2>Frequently Asked Questions</h2>
				<div itemscope="" itemtype="https://schema.org/FAQPage" id="faqs" class="accordion accordion-flush">
					<?php foreach ( $term_faqs as $index => $faq ) : ?>
						<?php
						$question = isset( $faq['question'] ) ? $faq['question'] : '';
						$answer   = isset( $faq['answer'] ) ? $faq['answer'] : '';
						if ( '' === trim( (string) $question ) && '' === trim( (string) $answer ) ) {
							continue;
						}
						?>
						<div class="faq__card accordion-item" itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
							<div class="accordion-header" id="heading<?= esc_attr( $index ); ?>">
								<button class="accordion-button collapsed question" type="button" data-bs-toggle="collapse" itemprop="name" data-bs-target="#faq-<?= esc_attr( $index ); ?>" aria-expanded="false">
									<h3><?= esc_html( $question ); ?></h3>
								</button>
							</div>
							<div class="answer accordion-collapse collapse" id="faq-<?= esc_attr( $index ); ?>" itemscope="" data-bs-parent="#faqs" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
								<div class="answer__inner" itemprop="text">
									<?= wp_kses_post( $answer ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
	// include lc-cta block.
	get_template_part( 'blocks/lc-cta' );
	?>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
	const viewButtons = document.querySelectorAll('.product-browser__switcher [data-view]');
	const browser = document.querySelector('.product-browser');
	const gridView = document.querySelector('.products-grid-view');
	const rowView = document.querySelector('.products-row-view');
	const storageKey = 'lcProductBrowserView';

	document.getElementById('skuSearch').addEventListener('input', filterCards);
	viewButtons.forEach(button => {
		button.addEventListener('click', () => setView(button.dataset.view));
	});
	document.getElementById('resetFilters').addEventListener('click', () => {
		document.getElementById('skuSearch').value = '';
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

	const rowItems = document.querySelectorAll('.product-card');
	const gridItems = document.querySelectorAll('.product-browser__item--grid');

	rowItems.forEach((row, index) => {
		const visible = row.dataset.sku.toLowerCase().includes(skuQuery);
		row.style.display = visible ? '' : 'none';
		if (gridItems[index]) {
			gridItems[index].style.display = visible ? '' : 'none';
		}
		visibleCount += visible ? 1 : 0;
	});

	document.getElementById('productCount').textContent = `${visibleCount} product${visibleCount !== 1 ? 's' : ''} found`;
}
</script>

<?php get_footer(); ?>
