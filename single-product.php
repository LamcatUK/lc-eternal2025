<?php
/**
 * Template for displaying single product posts.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>
<main id="main" class="single-product py-5">
	<?php
	while ( have_posts() ) {
		the_post();

		$product_id   = get_the_ID();
		$sku          = get_the_title( $product_id );
		$product_name = lc_get_product_display_name( $product_id );
		$img          = get_the_post_thumbnail( $product_id, 'large', array( 'class' => 'single-product__image img-fluid' ) );

		// Get ACF fields.
		$description      = get_field( 'description', $product_id );
		$brand_name       = get_field( 'brand_name', $product_id );
		$material         = get_field( 'material', $product_id );
		$size             = get_field( 'size', $product_id );
		$size_units       = get_field( 'size_units', $product_id );
		$micron           = get_field( 'micron', $product_id );
		$gsm              = get_field( 'gsm', $product_id );
		$colour           = get_field( 'colour', $product_id );
		$uom              = get_field( 'uom', $product_id );
		$pack_size        = get_field( 'pack_size', $product_id );
		$packs_per_case   = get_field( 'packs_per_case', $product_id );
		$cases_per_pallet = get_field( 'cases_per_pallet', $product_id );
		$compartments     = get_field( 'compartments', $product_id );
		$close_depth      = get_field( 'close_depth', $product_id );
		$weight_min       = get_field( 'weight_min', $product_id );
		$weight_max       = get_field( 'weight_max', $product_id );
		$product_types    = get_the_terms( $product_id, 'product_category' );
		$weight_parts     = array_filter( array( $weight_min, $weight_max ), 'strlen' );
		$weight_value     = ! empty( $weight_parts ) ? implode( ' / ', $weight_parts ) . ' g' : '';

		// Get product category for breadcrumbs.
		$categories = get_the_terms( $product_id, 'product_category' );
		?>
	<section class="breadcrumbs container pb-3">
		<p id="breadcrumbs">
			<?php
			if ( $categories && ! is_wp_error( $categories ) ) {
				$category = array_shift( $categories );
				?>
				<a href="/products/">Products</a>
				<span class="breadcrumb-separator"> / </span>
				<a href="/type/<?= esc_attr( $category->slug ); ?>/"><?= esc_html( $category->name ); ?></a>
				<span class="breadcrumb-separator"> / </span>
				<?= esc_html( $product_name ); ?>
				<?php
			} else {
				?>
				<a href="<?= esc_url( get_post_type_archive_link( 'product' ) ); ?>">Products</a>
				<span class="breadcrumb-separator"> / </span>
				<?php
			}
			?>
		</p>
	</section>
		
	<div class="container">
		<h1 class="single-product__title mb-4"><?= esc_html( $product_name ); ?> - <?= esc_html( $sku ); ?></h1>
		
		<div class="row g-4">
			<div class="col-lg-5">
					<?php
					if ( $img ) {
						echo wp_kses_post( $img );
					} else {
						?>
				<img
					src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>"
					class="single-product__image img-fluid"
					alt="<?= esc_attr( $product_name ); ?>">
						<?php
					}
					?>
			</div>
				
			<div class="col-lg-7">
				<div class="single-product__details">
					<h2 class="h4 mb-3">Product Details</h2>
					
					<table class="table table-bordered">
						<tbody>
							<?php
							if ( $sku ) {
								?>
							<tr>
								<th style="width: 40%;">Product Code</th>
								<td><?= esc_html( $sku ); ?></td>
							</tr>
								<?php
							}
							if ( $brand_name ) {
								?>
							<tr>
								<th>Brand Name</th>
								<td><?= esc_html( $brand_name ); ?></td>
							</tr>
								<?php
							}
							if ( $material ) {
								?>
							<tr>
								<th>Material</th>
								<td><?= esc_html( $material ); ?></td>
							</tr>
								<?php
							}
							if ( $size ) {
								?>
							<tr>
								<th>Size</th>
								<td><?= esc_html( $size ); ?> <?= esc_html( strtolower( $size_units ) ); ?></td>
							</tr>
								<?php
							}
							if ( $close_depth ) {
								?>
							<tr>
								<th>Close Depth</th>
								<td><?= esc_html( $close_depth ); ?> mm</td>
							</tr>
								<?php
							}
							if ( $micron ) {
								?>
							<tr>
								<th>Micron</th>
								<td><?= esc_html( $micron ); ?></td>
							</tr>
								<?php
							}
							if ( $gsm ) {
								?>
							<tr>
								<th>GSM</th>
								<td><?= esc_html( $gsm ); ?></td>
							</tr>
								<?php
							}
							if ( $colour ) {
								?>
							<tr>
								<th>Colour</th>
								<td><?= esc_html( $colour ); ?></td>
							</tr>
								<?php
							}
							if ( $uom ) {
								?>
							<tr>
								<th>Unit of Measure</th>
								<td><?= esc_html( $uom ); ?></td>
							</tr>
								<?php
							}
							if ( $compartments ) {
								?>
							<tr>
								<th>Compartments</th>
								<td><?= esc_html( $compartments ); ?></td>
							</tr>
								<?php
							}
							if ( $weight_value ) {
								?>
							<tr>
								<th>Weight</th>
								<td><?= esc_html( $weight_value ); ?></td>
							</tr>
								<?php
							}
							if ( $pack_size ) {
								?>
							<tr>
								<th>Pack Size</th>
								<td><?= esc_html( $pack_size ); ?></td>
							</tr>
								<?php
							}
							if ( $packs_per_case ) {
								?>
							<tr>
								<th>Packs per Case</th>
								<td><?= esc_html( $packs_per_case ); ?></td>
							</tr>
								<?php
							}
							if ( $cases_per_pallet ) {
								?>
							<tr>
								<th>Cases per Pallet</th>
								<td><?= esc_html( $cases_per_pallet ); ?></td>
							</tr>
								<?php
							}
							if ( $product_types && ! is_wp_error( $product_types ) ) {
								?>
							<tr>
								<th>Type</th>
								<td><?= esc_html( implode( ', ', wp_list_pluck( $product_types, 'name' ) ) ); ?></td>
							</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<a href="/contact-us/" class="ep-button ep-button--primary mt-3">Contact Us</a>
				</div>
			</div>
		</div>
		<?php
		if ( $description ) {
			?>
			<div class="row mt-5">
				<div class="col-12">
					<h2 class="h4 mb-3">Description</h2>
					<div class="single-product__description">
						<?= wp_kses_post( wpautop( $description ) ); ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
		<?php
	}
	?>
	<div class="container mt-5">
		<h3 class="h4 mb-4">Related Products</h3>
		<?php
		// Get the current product's category.
		$product_categories = get_the_terms( $product_id, 'product_category' );

		if ( $product_categories && ! is_wp_error( $product_categories ) ) {
			$category_ids = wp_list_pluck( $product_categories, 'term_id' );

			// Query related products.
			$related_args = array(
				'post_type'      => 'product',
				'posts_per_page' => 4,
				'post_status'    => 'publish',
				'post__not_in'   => array( $product_id ),
				'orderby'        => 'rand',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_category',
						'field'    => 'term_id',
						'terms'    => $category_ids,
					),
				),
			);

			$related_products = new WP_Query( $related_args );

			if ( $related_products->have_posts() ) {
				?>
				<div class="row g-4">
					<?php
					while ( $related_products->have_posts() ) {
						$related_products->the_post();
						$related_id = get_the_ID();
						$related_name = lc_get_product_display_name( $related_id );
						$related_sku = get_the_title( $related_id );
						$related_material = get_field( 'material', $related_id );
						$related_colour = get_field( 'colour', $related_id );
						$related_details = array();
						if ( $related_material ) {
							$related_details[] = $related_material;
						}
						if ( $related_colour ) {
							$related_details[] = $related_colour;
						}
						?>
						<div class="col-md-6 col-lg-3">
							<a href="<?= esc_url( get_permalink() ); ?>" class="lc-product-card related-product">
								<div class="lc-product-card__image related-product__image">
									<?php
									if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'medium' );
									} else {
										?>
										<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" alt="<?= esc_attr( $related_name ); ?>">
										<?php
									}
									?>
								</div>
								<div class="lc-product-card__body">
									<h4 class="lc-product-card__title"><?= esc_html( $related_name ); ?></h4>
									<p class="lc-product-card__sku"><?= esc_html( $related_sku ); ?></p>
									<?php if ( ! empty( $related_details ) ) : ?>
										<p class="lc-product-card__meta text-muted small"><?= esc_html( implode( ' • ', $related_details ) ); ?></p>
									<?php endif; ?>
								</div>
							</a>
						</div>
						<?php
					}
					wp_reset_postdata();
					?>
				</div>
				<?php
			} else {
				echo '<p>No related products found.</p>';
			}
		}
		?>
	</div>
</main>
<?php
get_footer();
