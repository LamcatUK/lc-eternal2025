<?php
/**
 * Product related functions.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add admin menu for product import.
 */
function lc_products_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=product',
		'Import Products',
		'Import Products',
		'manage_options',
		'lc-import-products',
		'lc_products_import_page'
	);
	
	add_submenu_page(
		'edit.php?post_type=product',
		'Bulk Assign Image',
		'Bulk Assign Image',
		'manage_options',
		'lc-bulk-assign-image',
		'lc_products_bulk_image_page'
	);
}
add_action( 'admin_menu', 'lc_products_admin_menu' );

/**
 * Render the import page.
 */
function lc_products_import_page() {
	?>
	<div class="wrap">
		<h1>Import Products</h1>
		
		<?php if ( isset( $_GET['imported'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $_GET['imported'] ); ?> products imported successfully!</p>
			</div>
		<?php endif; ?>
		
		<?php if ( isset( $_GET['error'] ) ) : ?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( urldecode( $_GET['error'] ) ); ?></p>
			</div>
		<?php endif; ?>
		
		<form method="post" enctype="multipart/form-data" action="">
			<?php wp_nonce_field( 'lc_import_products', 'lc_import_nonce' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="product_csv">CSV File</label>
					</th>
					<td>
						<input type="file" name="product_csv" id="product_csv" accept=".csv" required>
						<p class="description">Upload a CSV file with product data. Expected columns: Product Code, Description, Brand Name, Material, Size, Size Unit, Micron, GSM, Colour, UOM, Pack size, Packs per case, Cases /Pallet</p>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="lc_import_submit" class="button button-primary" value="Import Products">
			</p>
		</form>
	</div>
	<?php
}

/**
 * Process the CSV import.
 */
function lc_products_process_import() {
	if ( ! isset( $_POST['lc_import_submit'] ) || ! isset( $_POST['lc_import_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['lc_import_nonce'], 'lc_import_products' ) ) {
		wp_die( 'Security check failed' );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}

	if ( empty( $_FILES['product_csv']['tmp_name'] ) ) {
		wp_redirect( add_query_arg( 'error', urlencode( 'No file uploaded' ), wp_get_referer() ) );
		exit;
	}

	$file   = $_FILES['product_csv']['tmp_name'];
	$handle = fopen( $file, 'r' );

	if ( ! $handle ) {
		wp_redirect( add_query_arg( 'error', urlencode( 'Could not open file' ), wp_get_referer() ) );
		exit;
	}

	// Read header row.
	$headers = fgetcsv( $handle );

	if ( ! $headers ) {
		fclose( $handle );
		wp_redirect( add_query_arg( 'error', urlencode( 'Invalid CSV format' ), wp_get_referer() ) );
		exit;
	}

	// Map headers to indices.
	$header_map = array_flip( $headers );

	$imported = 0;

	while ( ( $row = fgetcsv( $handle ) ) !== false ) {
		// Skip empty rows.
		if ( empty( array_filter( $row ) ) ) {
			continue;
		}

		// Extract product code (used as title and slug).
		$product_code = isset( $row[ $header_map['Product Code'] ] ) ? trim( $row[ $header_map['Product Code'] ] ) : '';

		if ( empty( $product_code ) ) {
			continue;
		}

		// Check if product already exists.
		$existing = get_page_by_path( sanitize_title( $product_code ), OBJECT, 'product' );

		if ( $existing ) {
			$post_id = $existing->ID;
		} else {
			// Create new product.
			$post_id = wp_insert_post(
				array(
					'post_title'  => $product_code,
					'post_type'   => 'product',
					'post_status' => 'publish',
				)
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}
		}

		// Update ACF fields.
		$field_mapping = array(
			'Description'    => 'description',
			'Brand Name'     => 'brand_name',
			'Material'       => 'material',
			'Size'           => 'size',
			'Size Unit'      => 'size_units',
			'Micron'         => 'micron',
			'GSM'            => 'gsm',
			'Colour'         => 'colour',
			'UOM'            => 'uom',
			'Pack size'      => 'pack_size',
			'Packs per case' => 'packs_per_case',
			'Cases /Pallet'  => 'cases_per_pallet',
		);

		foreach ( $field_mapping as $csv_column => $acf_field ) {
			if ( isset( $header_map[ $csv_column ] ) && isset( $row[ $header_map[ $csv_column ] ] ) ) {
				$value = trim( $row[ $header_map[ $csv_column ] ] );

				// Skip empty values.
				if ( '' === $value ) {
					continue;
				}

				// Convert numeric fields.
				if ( in_array( $acf_field, array( 'micron', 'gsm', 'packs_per_case', 'cases_per_pallet' ), true ) ) {
					$value = is_numeric( $value ) ? floatval( $value ) : 0;
				}

				update_field( $acf_field, $value, $post_id );
			}
		}

		++$imported;
	}

	fclose( $handle );

	wp_redirect( add_query_arg( 'imported', $imported, wp_get_referer() ) );
	exit;
}
add_action( 'admin_init', 'lc_products_process_import' );

/**
 * Render the bulk image assignment page.
 */
function lc_products_bulk_image_page() {
	// Enqueue media uploader scripts.
	wp_enqueue_media();
	
	// Get all product categories for filtering.
	$categories = get_terms(
		array(
			'taxonomy'   => 'product_category',
			'hide_empty' => false,
		)
	);
	
	// Get selected category if any.
	$selected_category = isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0;
	$search_term = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
	
	// Query products.
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	
	if ( $selected_category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_category',
				'field'    => 'term_id',
				'terms'    => $selected_category,
			),
		);
	}
	
	if ( $search_term ) {
		// Search in title using standard WordPress search.
		$args['s'] = $search_term;
		
		// Also get products matching meta fields.
		$meta_args = $args;
		unset( $meta_args['s'] );
		$meta_args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => 'description',
				'value'   => $search_term,
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'brand_name',
				'value'   => $search_term,
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'material',
				'value'   => $search_term,
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'colour',
				'value'   => $search_term,
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'size',
				'value'   => $search_term,
				'compare' => 'LIKE',
			),
		);
		
		// Get both sets and merge.
		$title_query = new WP_Query( $args );
		$meta_query = new WP_Query( $meta_args );
		
		// Combine and deduplicate.
		$all_products = array_merge( $title_query->posts, $meta_query->posts );
		$product_ids = array();
		$products = array();
		
		foreach ( $all_products as $product ) {
			if ( ! in_array( $product->ID, $product_ids, true ) ) {
				$product_ids[] = $product->ID;
				$products[] = $product;
			}
		}
		
		// Sort by title.
		usort(
			$products,
			function ( $a, $b ) {
				return strcmp( $a->post_title, $b->post_title );
			}
		);
	} else {
		$query = new WP_Query( $args );
		$products = $query->posts;
	}
	?>
	<div class="wrap">
		<h1>Bulk Assign Featured Image</h1>
		
		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $_GET['updated'] ); ?> products updated successfully!</p>
			</div>
		<?php endif; ?>
		
		<?php if ( isset( $_GET['error'] ) ) : ?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( urldecode( $_GET['error'] ) ); ?></p>
			</div>
		<?php endif; ?>
		
		<form method="get" action="" style="margin-bottom: 20px;">
			<input type="hidden" name="post_type" value="product">
			<input type="hidden" name="page" value="lc-bulk-assign-image">
			
			<div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
				<div>
					<label for="search">Search: </label>
					<input type="text" name="search" id="search" value="<?= esc_attr( $search_term ); ?>" placeholder="Product code or description">
				</div>
				
				<div>
					<label for="category">Category: </label>
					<select name="category" id="category">
						<option value="0">All Products</option>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?= esc_attr( $cat->term_id ); ?>" <?php selected( $selected_category, $cat->term_id ); ?>>
								<?= esc_html( $cat->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<button type="submit" class="button">Filter</button>
				
				<?php if ( $search_term || $selected_category ) : ?>
					<a href="?post_type=product&page=lc-bulk-assign-image" class="button">Clear Filters</a>
				<?php endif; ?>
			</div>
		</form>
		
		<form method="post" action="">
			<?php wp_nonce_field( 'lc_bulk_assign_image', 'lc_bulk_image_nonce' ); ?>
			
			<div style="margin: 20px 0; padding: 15px; background: #f0f0f1; border: 1px solid #c3c4c7;">
				<button type="button" class="button button-large" id="select-featured-image">Select Featured Image</button>
				<input type="hidden" name="featured_image_id" id="featured-image-id" value="">
				<div id="image-preview" style="margin-top: 10px;"></div>
			</div>
			
			<p><strong>Select products to update (<?= count( $products ); ?> products shown):</strong></p>
			
			<div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: center;">
				<button type="button" class="button" id="select-all-btn">Select All</button>
				<button type="button" class="button" id="deselect-all-btn">Deselect All</button>
				<button type="button" class="button" id="select-no-image-btn">Select Products Without Image</button>
				<button type="button" class="button" id="select-with-image-btn">Select Products With Image</button>
			</div>
			
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th style="width: 40px;"><input type="checkbox" id="select-all-header"></th>
						<th>Product Code</th>
						<th>Description</th>
						<th style="width: 120px;">Current Image</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $products ) ) : ?>
						<tr>
							<td colspan="4">No products found.</td>
						</tr>
					<?php else : ?>
						<?php foreach ( $products as $product ) : ?>
							<?php $has_image = has_post_thumbnail( $product->ID ); ?>
							<tr>
								<td>
									<input type="checkbox" name="product_ids[]" value="<?= esc_attr( $product->ID ); ?>" class="product-checkbox" data-has-image="<?= $has_image ? '1' : '0'; ?>">
								</td>
								<td><?= esc_html( $product->post_title ); ?></td>
								<td><?= wp_kses_post( wp_trim_words( get_field( 'description', $product->ID ), 10 ) ); ?></td>
								<td>
									<?php
									if ( $has_image ) {
										echo get_the_post_thumbnail( $product->ID, 'thumbnail' );
									} else {
										echo '<em>No image</em>';
									}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" name="lc_bulk_image_submit" class="button button-primary" value="Assign Image to Selected Products">
			</p>
		</form>
		
		<script>
		jQuery(document).ready(function($) {
			var mediaUploader;
			
			// Header checkbox select all
			$('#select-all-header').on('change', function() {
				$('.product-checkbox').prop('checked', this.checked);
			});
			
			// Select all button
			$('#select-all-btn').on('click', function(e) {
				e.preventDefault();
				$('.product-checkbox').prop('checked', true);
				$('#select-all-header').prop('checked', true);
			});
			
			// Deselect all button
			$('#deselect-all-btn').on('click', function(e) {
				e.preventDefault();
				$('.product-checkbox').prop('checked', false);
				$('#select-all-header').prop('checked', false);
			});
			
			// Select products without image
			$('#select-no-image-btn').on('click', function(e) {
				e.preventDefault();
				$('.product-checkbox').each(function() {
					$(this).prop('checked', $(this).data('has-image') === 0);
				});
			});
			
			// Select products with image
			$('#select-with-image-btn').on('click', function(e) {
				e.preventDefault();
				$('.product-checkbox').each(function() {
					$(this).prop('checked', $(this).data('has-image') === 1);
				});
			});
			
			// Media uploader
			$('#select-featured-image').on('click', function(e) {
				e.preventDefault();
				
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}
				
				mediaUploader = wp.media({
					title: 'Select Featured Image',
					button: {
						text: 'Use this image'
					},
					multiple: false
				});
				
				mediaUploader.on('select', function() {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					$('#featured-image-id').val(attachment.id);
					$('#image-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; border: 1px solid #ddd;">');
				});
				
				mediaUploader.open();
			});
		});
		</script>
	</div>
	<?php
}

/**
 * Process bulk image assignment.
 */
function lc_products_process_bulk_image() {
	if ( ! isset( $_POST['lc_bulk_image_submit'] ) || ! isset( $_POST['lc_bulk_image_nonce'] ) ) {
		return;
	}
	
	if ( ! wp_verify_nonce( $_POST['lc_bulk_image_nonce'], 'lc_bulk_assign_image' ) ) {
		wp_die( 'Security check failed' );
	}
	
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}
	
	$image_id = isset( $_POST['featured_image_id'] ) ? intval( $_POST['featured_image_id'] ) : 0;
	$product_ids = isset( $_POST['product_ids'] ) ? array_map( 'intval', $_POST['product_ids'] ) : array();
	
	if ( ! $image_id ) {
		wp_redirect( add_query_arg( 'error', urlencode( 'Please select an image' ), wp_get_referer() ) );
		exit;
	}
	
	if ( empty( $product_ids ) ) {
		wp_redirect( add_query_arg( 'error', urlencode( 'Please select at least one product' ), wp_get_referer() ) );
		exit;
	}
	
	$updated = 0;
	
	foreach ( $product_ids as $product_id ) {
		if ( set_post_thumbnail( $product_id, $image_id ) ) {
			++$updated;
		}
	}
	
	wp_redirect( add_query_arg( 'updated', $updated, wp_get_referer() ) );
	exit;
}
add_action( 'admin_init', 'lc_products_process_bulk_image' );
