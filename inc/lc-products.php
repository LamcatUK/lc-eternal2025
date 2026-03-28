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
 * Get CSV to ACF field mapping for product imports.
 *
 * @return array<string, string>
 */
function lc_products_import_field_mapping() {
	return array(
		'Description'    => 'description',
		'Brand Name'     => 'brand_name',
		'Material'       => 'material',
		'Size'           => 'size',
		'Size Units'     => 'size_units',
		'Size Unit'      => 'size_units',
		'Micron'         => 'micron',
		'GSM'            => 'gsm',
		'Colour'         => 'colour',
		'UOM'            => 'uom',
		'Pack size'      => 'pack_size',
		'Packs per case' => 'packs_per_case',
		'Cases /Pallet'  => 'cases_per_pallet',
		'Compartments'   => 'compartments',
		'Close depth'    => 'close_depth',
		'Weight min'     => 'weight_min',
		'Weight max'     => 'weight_max',
	);
}

/**
 * Get the import page URL.
 *
 * @param array $args Optional query args.
 * @return string
 */
function lc_products_import_page_url( $args = array() ) {
	$url = admin_url( 'edit.php?post_type=product&page=lc-import-products' );

	if ( ! empty( $args ) ) {
		$url = add_query_arg( $args, $url );
	}

	return $url;
}

/**
 * Get the import mode from the request.
 *
 * @return string
 */
function lc_products_get_import_mode() {
	$mode = isset( $_POST['lc_import_mode'] ) ? sanitize_key( wp_unslash( $_POST['lc_import_mode'] ) ) : '';

	return in_array( $mode, array( 'merge', 'replace' ), true ) ? $mode : 'merge';
}

/**
 * Get the missing product action for replace mode.
 *
 * @return string
 */
function lc_products_get_missing_product_action() {
	$action = isset( $_POST['lc_missing_product_action'] ) ? sanitize_key( wp_unslash( $_POST['lc_missing_product_action'] ) ) : '';

	return in_array( $action, array( 'draft', 'trash', 'delete' ), true ) ? $action : 'trash';
}

/**
 * Whether to remove featured images from products removed during replace imports.
 *
 * @return bool
 */
function lc_products_should_remove_missing_images() {
	return ! empty( $_POST['lc_remove_missing_images'] );
}

/**
 * Get existing products keyed by import lookup key.
 *
 * @return array<string, WP_Post>
 */
function lc_products_get_existing_products_index() {
	$products = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$indexed_products = array();

	foreach ( $products as $product ) {
		$indexed_products[ $product->post_name ] = $product;

		$title_key = sanitize_title( $product->post_title );
		if ( '' !== $title_key && ! isset( $indexed_products[ $title_key ] ) ) {
			$indexed_products[ $title_key ] = $product;
		}
	}

	return $indexed_products;
}

/**
 * Build a product label for reporting.
 *
 * @param WP_Post $product Product post object.
 * @return string
 */
function lc_products_format_report_product_label( $product ) {
	return $product->post_title . ' (#' . $product->ID . ')';
}

/**
 * Build the text label for a missing product action.
 *
 * @param string $action Missing product action.
 * @return string
 */
function lc_products_get_missing_action_label( $action ) {
	if ( 'draft' === $action ) {
		return 'Move to draft';
	}

	if ( 'delete' === $action ) {
		return 'Delete permanently';
	}

	return 'Move to trash';
}

/**
 * Apply the chosen replace-mode action to a missing product.
 *
 * @param int    $product_id             Product ID.
 * @param string $missing_product_action Action to take.
 * @param bool   $remove_missing_images  Whether to remove the featured image first.
 * @return bool
 */
function lc_products_apply_missing_product_action( $product_id, $missing_product_action, $remove_missing_images ) {
	if ( $remove_missing_images ) {
		delete_post_thumbnail( $product_id );
	}

	if ( 'draft' === $missing_product_action ) {
		$result = wp_update_post(
			array(
				'ID'          => $product_id,
				'post_status' => 'draft',
			),
			true
		);

		return ! is_wp_error( $result );
	}

	if ( 'delete' === $missing_product_action ) {
		return (bool) wp_delete_post( $product_id, true );
	}

	return (bool) wp_trash_post( $product_id );
}

/**
 * Normalize imported product field values for comparison and saving.
 *
 * @param string $acf_field The ACF field name.
 * @param string $value     The incoming CSV value.
 * @return string
 */
function lc_products_normalize_import_value( $acf_field, $value ) {
	$value = trim( (string) $value );

	if ( lc_products_is_numeric_import_field( $acf_field ) && is_numeric( $value ) ) {
		$number = (float) $value;

		if ( (float) (int) $number === $number ) {
			return (string) (int) $number;
		}

		return rtrim( rtrim( (string) $number, '0' ), '.' );
	}

	$value = wp_strip_all_tags( html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	$value = str_replace( array( "\xc2\xa0", "\xe2\x80\x8b", "\xe2\x80\x8c", "\xe2\x80\x8d", "\xef\xbb\xbf" ), ' ', $value );
	$value = preg_replace( '/[[:space:]]+/u', ' ', $value );
	$value = trim( $value );

	return $value;
}

/**
 * Determine whether an import field expects a numeric value.
 *
 * @param string $acf_field The ACF field name.
 * @return bool
 */
function lc_products_is_numeric_import_field( $acf_field ) {
	return in_array( $acf_field, array( 'micron', 'gsm', 'packs_per_case', 'cases_per_pallet', 'compartments', 'close_depth', 'weight_min', 'weight_max' ), true );
}

/**
 * Get a raw CSV value using one or more possible header names.
 *
 * @param array<string, int> $header_map CSV header index map.
 * @param array<int, string> $row        Raw CSV row.
 * @param string|array       $headers    Accepted header name(s).
 * @return string
 */
function lc_products_get_row_raw_value( $header_map, $row, $headers ) {
	$headers = (array) $headers;

	foreach ( $headers as $header ) {
		if ( isset( $header_map[ $header ], $row[ $header_map[ $header ] ] ) ) {
			return trim( (string) $row[ $header_map[ $header ] ] );
		}
	}

	return '';
}

/**
 * Normalize stored field values for dry run comparisons.
 *
 * @param string     $acf_field     The ACF field name.
 * @param mixed|null $current_value The currently stored value.
 * @return string
 */
function lc_products_normalize_current_value( $acf_field, $current_value ) {
	if ( null === $current_value ) {
		return '';
	}

	if ( is_scalar( $current_value ) ) {
		return lc_products_normalize_import_value( $acf_field, (string) $current_value );
	}

	return trim( wp_json_encode( $current_value ) );
}

/**
 * Build normalized import field values for a CSV row.
 *
 * @param array<string, int>    $header_map     CSV header index map.
 * @param array<int, string>    $row            Raw CSV row.
 * @param array<string, string> $field_mapping  CSV-to-ACF field mapping.
 * @return array<string, string>
 */
function lc_products_get_row_field_values( $header_map, $row, $field_mapping ) {
	$field_values  = array();
	$field_headers = array(
		'size_units' => array( 'Size Units', 'Size Unit' ),
	);

	foreach ( $field_mapping as $csv_column => $acf_field ) {
		$raw_value = isset( $field_headers[ $acf_field ] )
			? lc_products_get_row_raw_value( $header_map, $row, $field_headers[ $acf_field ] )
			: lc_products_get_row_raw_value( $header_map, $row, $csv_column );

		$field_values[ $acf_field ] = lc_products_normalize_import_value( $acf_field, $raw_value );
	}

	return $field_values;
}

/**
 * Normalize a product category import slug.
 *
 * @param string $value Raw CSV value.
 * @return string
 */
function lc_products_normalize_import_category_slug( $value ) {
	return sanitize_title( lc_products_normalize_import_value( 'product_category', $value ) );
}

/**
 * Build normalized stored field values for a product.
 *
 * @param int                   $product_id     Product ID.
 * @param array<string, string> $field_mapping CSV-to-ACF field mapping.
 * @return array<string, string>
 */
function lc_products_get_product_field_values( $product_id, $field_mapping ) {
	$field_values = array();

	foreach ( $field_mapping as $acf_field ) {
		$field_values[ $acf_field ] = lc_products_normalize_current_value( $acf_field, get_field( $acf_field, $product_id ) );
	}

	return $field_values;
}

/**
 * Create a stable fingerprint for matching products when codes change.
 *
 * @param array<string, string> $field_values Normalized field values.
 * @return string
 */
function lc_products_get_match_fingerprint( $field_values ) {
	$non_empty_fields = array_filter(
		$field_values,
		function ( $value ) {
			return '' !== $value;
		}
	);

	if ( count( $non_empty_fields ) < 3 ) {
		return '';
	}

	return md5( wp_json_encode( $field_values ) );
}

/**
 * Build an index of existing products by their normalized field fingerprint.
 *
 * @param array<string, WP_Post> $existing_products Existing product index.
 * @param array<string, string>  $field_mapping     CSV-to-ACF field mapping.
 * @return array<string, array<int, WP_Post>>
 */
function lc_products_get_existing_products_fingerprint_index( $existing_products, $field_mapping ) {
	$fingerprint_index = array();
	$processed_ids     = array();

	foreach ( $existing_products as $product ) {
		if ( isset( $processed_ids[ $product->ID ] ) ) {
			continue;
		}

		$processed_ids[ $product->ID ] = true;
		$fingerprint                   = lc_products_get_match_fingerprint( lc_products_get_product_field_values( $product->ID, $field_mapping ) );

		if ( '' === $fingerprint ) {
			continue;
		}

		if ( ! isset( $fingerprint_index[ $fingerprint ] ) ) {
			$fingerprint_index[ $fingerprint ] = array();
		}

		$fingerprint_index[ $fingerprint ][] = $product;
	}

	return $fingerprint_index;
}

/**
 * Find a unique unmatched existing product by fingerprint.
 *
 * @param string                             $fingerprint        Row fingerprint.
 * @param array<string, array<int, WP_Post>> $fingerprint_index Existing fingerprint index.
 * @param array<int, bool>                   $matched_product_ids Matched product IDs.
 * @return WP_Post|null
 */
function lc_products_find_product_by_fingerprint( $fingerprint, $fingerprint_index, $matched_product_ids ) {
	if ( '' === $fingerprint || empty( $fingerprint_index[ $fingerprint ] ) ) {
		return null;
	}

	$candidates = array_values(
		array_filter(
			$fingerprint_index[ $fingerprint ],
			function ( $product ) use ( $matched_product_ids ) {
				return ! isset( $matched_product_ids[ $product->ID ] );
			}
		)
	);

	return 1 === count( $candidates ) ? $candidates[0] : null;
}

/**
 * Get stored dry run report for the current user.
 *
 * @return array|null
 */
function lc_products_get_dry_run_report() {
	$report_key = isset( $_GET['dry_run_report'] ) ? sanitize_key( wp_unslash( $_GET['dry_run_report'] ) ) : '';

	if ( '' === $report_key ) {
		return null;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return null;
	}

	$transient_key = 'lc_product_dry_run_' . $user_id . '_' . $report_key;
	$report        = get_transient( $transient_key );

	if ( ! is_array( $report ) ) {
		return null;
	}

	delete_transient( $transient_key );

	return $report;
}

/**
 * Render the import page.
 */
function lc_products_import_page() {
	$dry_run_report                  = lc_products_get_dry_run_report();
	$selected_mode                   = isset( $_GET['import_mode'] ) ? sanitize_key( wp_unslash( $_GET['import_mode'] ) ) : 'merge';
	$selected_mode                   = in_array( $selected_mode, array( 'merge', 'replace' ), true ) ? $selected_mode : 'merge';
	$selected_missing_product_action = 'trash';
	$selected_remove_missing_images  = false;

	if ( ! empty( $dry_run_report ) ) {
		$selected_missing_product_action = $dry_run_report['summary']['missing_product_action'];
		$selected_remove_missing_images  = ! empty( $dry_run_report['summary']['remove_missing_images'] );
	}
	?>
	<div class="wrap">
		<h1>Import Products</h1>
		
		<?php if ( isset( $_GET['imported'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					Import complete.
					Processed: <?php echo esc_html( isset( $_GET['processed'] ) ? wp_unslash( $_GET['processed'] ) : $_GET['imported'] ); ?> |
					Created: <?php echo esc_html( isset( $_GET['created'] ) ? wp_unslash( $_GET['created'] ) : '0' ); ?> |
					Updated: <?php echo esc_html( isset( $_GET['updated'] ) ? wp_unslash( $_GET['updated'] ) : '0' ); ?> |
					Unchanged: <?php echo esc_html( isset( $_GET['unchanged'] ) ? wp_unslash( $_GET['unchanged'] ) : '0' ); ?> |
					Skipped: <?php echo esc_html( isset( $_GET['skipped'] ) ? wp_unslash( $_GET['skipped'] ) : '0' ); ?> |
					Removed: <?php echo esc_html( isset( $_GET['removed'] ) ? wp_unslash( $_GET['removed'] ) : '0' ); ?>
				</p>
			</div>
		<?php endif; ?>
		
		<?php if ( isset( $_GET['error'] ) ) : ?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( urldecode( $_GET['error'] ) ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $dry_run_report ) ) : ?>
			<div class="notice notice-info">
				<p><strong>Dry run complete.</strong> No products were created, updated, or removed.</p>
				<p>
					Mode: <?php echo esc_html( ucfirst( $dry_run_report['summary']['mode'] ) ); ?> |
					Processed: <?php echo esc_html( $dry_run_report['summary']['processed'] ); ?> |
					Create: <?php echo esc_html( $dry_run_report['summary']['create'] ); ?> |
					Update: <?php echo esc_html( $dry_run_report['summary']['update'] ); ?> |
					Unchanged: <?php echo esc_html( $dry_run_report['summary']['unchanged'] ); ?> |
					Skip: <?php echo esc_html( $dry_run_report['summary']['skip'] ); ?> |
					Remove: <?php echo esc_html( $dry_run_report['summary']['remove'] ); ?>
				</p>
				<?php if ( 'replace' === $dry_run_report['summary']['mode'] ) : ?>
					<p>
						Missing product action: <?php echo esc_html( lc_products_get_missing_action_label( $dry_run_report['summary']['missing_product_action'] ) ); ?> |
						Images for removed products: <?php echo esc_html( $dry_run_report['summary']['remove_missing_images'] ? 'Remove featured images first' : 'Keep featured images unchanged' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $dry_run_report['rows'] ) ) : ?>
				<h2>Dry Run Preview</h2>
				<p class="description">This preview first matches by product code, then falls back to an exact unique field fingerprint to detect likely code changes.</p>
				<table class="widefat striped">
					<thead>
						<tr>
							<th>Row</th>
							<th>Product Code</th>
							<th>Matched Product</th>
							<th>Action</th>
							<th>Changes</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $dry_run_report['rows'] as $result_row ) : ?>
							<tr>
								<td><?php echo esc_html( $result_row['row_number'] ); ?></td>
								<td><?php echo esc_html( $result_row['product_code'] ); ?></td>
								<td>
									<?php if ( ! empty( $result_row['matched_product'] ) ) : ?>
										<?php echo esc_html( $result_row['matched_product'] ); ?>
									<?php else : ?>
										-
									<?php endif; ?>
								</td>
								<td><strong><?php echo esc_html( ucfirst( $result_row['action'] ) ); ?></strong></td>
								<td>
									<?php if ( ! empty( $result_row['changes'] ) ) : ?>
										<ul style="margin: 0; padding-left: 18px;">
											<?php foreach ( $result_row['changes'] as $change ) : ?>
												<li><?php echo esc_html( $change ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php elseif ( ! empty( $result_row['message'] ) ) : ?>
										<?php echo esc_html( $result_row['message'] ); ?>
									<?php else : ?>
										-
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>
		
		<form method="post" enctype="multipart/form-data" action="">
			<?php wp_nonce_field( 'lc_import_products', 'lc_import_nonce' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">Import mode</th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="lc_import_mode" value="merge" <?php checked( 'replace' !== $selected_mode ); ?>>
								Merge
							</label>
							<br>
							<label>
								<input type="radio" name="lc_import_mode" value="replace" <?php checked( 'replace' === $selected_mode ); ?>>
								Replace
							</label>
							<p class="description">Merge creates and updates products. Replace also removes existing products that are not present in the CSV.</p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="product_csv">CSV File</label>
					</th>
					<td>
						<input type="file" name="product_csv" id="product_csv" accept=".csv" required>
						<p class="description">Upload a CSV file with product data. Supported columns include Product Code, Description, Brand Name, Material, Size, Size Unit or Size Units, Micron, GSM, Colour, UOM, Pack size, Packs per case, Cases /Pallet, Compartments, Close depth, Weight min, Weight max, and Type.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Replace options</th>
					<td>
						<select name="lc_missing_product_action">
							<option value="trash" <?php selected( 'trash', $selected_missing_product_action ); ?>>Move missing products to trash</option>
							<option value="draft" <?php selected( 'draft', $selected_missing_product_action ); ?>>Move missing products to draft</option>
							<option value="delete" <?php selected( 'delete', $selected_missing_product_action ); ?>>Delete missing products permanently</option>
						</select>
						<p class="description">Used only in Replace mode.</p>
						<label>
							<input type="checkbox" name="lc_remove_missing_images" value="1" <?php checked( $selected_remove_missing_images ); ?>>
							Also remove featured images from products being removed by Replace mode
						</label>
						<p class="description">By default, product featured images are left untouched.</p>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="lc_import_submit" class="button button-primary" value="Import Products">
				<input type="submit" name="lc_import_dry_run" class="button button-secondary" value="Dry Run Import">
			</p>
		</form>
	</div>
	<?php
}

/**
 * Process the CSV import.
 */
function lc_products_process_import() {
	if ( ! isset( $_POST['lc_import_nonce'] ) || ( ! isset( $_POST['lc_import_submit'] ) && ! isset( $_POST['lc_import_dry_run'] ) ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['lc_import_nonce'], 'lc_import_products' ) ) {
		wp_die( 'Security check failed' );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}

	if ( empty( $_FILES['product_csv']['tmp_name'] ) ) {
		wp_redirect( lc_products_import_page_url( array( 'error' => rawurlencode( 'No file uploaded' ) ) ) );
		exit;
	}

	$file   = $_FILES['product_csv']['tmp_name'];
	$handle = fopen( $file, 'r' );

	if ( ! $handle ) {
		wp_redirect( lc_products_import_page_url( array( 'error' => rawurlencode( 'Could not open file' ) ) ) );
		exit;
	}

	// Read header row.
	$headers = fgetcsv( $handle );

	if ( ! $headers ) {
		fclose( $handle );
		wp_redirect( lc_products_import_page_url( array( 'error' => rawurlencode( 'Invalid CSV format' ) ) ) );
		exit;
	}

	// Map headers to indices.
	$header_map = array();

	foreach ( $headers as $index => $header ) {
		$header_map[ trim( $header ) ] = $index;
	}

	if ( ! isset( $header_map['Product Code'] ) ) {
		fclose( $handle );
		wp_redirect( lc_products_import_page_url( array( 'error' => rawurlencode( 'Missing required Product Code column' ) ) ) );
		exit;
	}

	$field_mapping          = lc_products_import_field_mapping();
	$dry_run                = isset( $_POST['lc_import_dry_run'] );
	$import_mode            = lc_products_get_import_mode();
	$missing_product_action = lc_products_get_missing_product_action();
	$remove_missing_images  = lc_products_should_remove_missing_images();
	$existing_products      = lc_products_get_existing_products_index();
	$fingerprint_index      = lc_products_get_existing_products_fingerprint_index( $existing_products, $field_mapping );
	$imported_product_slugs = array();
	$matched_product_ids    = array();

	$imported     = 0;
	$summary      = array(
		'mode'                   => $import_mode,
		'missing_product_action' => $missing_product_action,
		'remove_missing_images'  => $remove_missing_images,
		'processed'              => 0,
		'create'                 => 0,
		'update'                 => 0,
		'unchanged'              => 0,
		'skip'                   => 0,
		'remove'                 => 0,
	);
	$dry_run_rows = array();
	$row_number   = 1;

	while ( ( $row = fgetcsv( $handle ) ) !== false ) {
		++$row_number;
		++$summary['processed'];

		// Skip empty rows.
		if ( empty( array_filter( $row ) ) ) {
			++$summary['skip'];
			if ( $dry_run ) {
				$dry_run_rows[] = array(
					'row_number'      => $row_number,
					'product_code'    => '',
					'matched_product' => '',
					'action'          => 'skip',
					'changes'         => array(),
					'message'         => 'Empty row',
				);
			}
			continue;
		}

		// Extract product code (used as title and slug).
		$product_code = isset( $row[ $header_map['Product Code'] ] ) ? trim( $row[ $header_map['Product Code'] ] ) : '';

		if ( empty( $product_code ) ) {
			++$summary['skip'];
			if ( $dry_run ) {
				$dry_run_rows[] = array(
					'row_number'      => $row_number,
					'product_code'    => '',
					'matched_product' => '',
					'action'          => 'skip',
					'changes'         => array(),
					'message'         => 'Missing Product Code',
				);
			}
			continue;
		}

		// Check if product already exists.
		$product_slug           = sanitize_title( $product_code );
		$row_field_values       = lc_products_get_row_field_values( $header_map, $row, $field_mapping );
		$row_fingerprint        = lc_products_get_match_fingerprint( $row_field_values );
		$row_type_slug          = lc_products_normalize_import_category_slug( lc_products_get_row_raw_value( $header_map, $row, 'Type' ) );
		$existing               = isset( $existing_products[ $product_slug ] ) ? $existing_products[ $product_slug ] : null;
		$matched_by_code_change = false;
		$changes                = array();
		$action                 = $existing ? 'unchanged' : 'create';

		if ( ! $existing ) {
			$existing = lc_products_find_product_by_fingerprint( $row_fingerprint, $fingerprint_index, $matched_product_ids );

			if ( $existing ) {
				$matched_by_code_change = true;
				$action                 = 'update';
				$changes[]              = sprintf( 'Product Code: %1$s -> %2$s', $existing->post_title, $product_code );
			}
		}

		if ( $existing ) {
			$imported_product_slugs[] = $existing->post_name;
			$imported_product_slugs[] = sanitize_title( $existing->post_title );
		} else {
			$imported_product_slugs[] = $product_slug;
		}

		if ( $existing ) {
			$post_id = $existing->ID;

			if ( $product_code !== $existing->post_title ) {
				$action = 'update';

				if ( ! $matched_by_code_change ) {
					$changes[] = sprintf( 'Product Code: %1$s -> %2$s', $existing->post_title, $product_code );
				}

				if ( ! $dry_run ) {
					$updated_post_id = wp_update_post(
						array(
							'ID'         => $post_id,
							'post_title' => $product_code,
							'post_name'  => $product_slug,
						),
						true
					);

					if ( is_wp_error( $updated_post_id ) ) {
						++$summary['skip'];

						if ( $dry_run ) {
							$dry_run_rows[] = array(
								'row_number'      => $row_number,
								'product_code'    => $product_code,
								'matched_product' => lc_products_format_report_product_label( $existing ),
								'action'          => 'skip',
								'changes'         => array(),
								'message'         => 'Could not update product code',
							);
						}

						continue;
					}

					$existing->post_title               = $product_code;
					$existing->post_name                = get_post_field( 'post_name', $post_id );
					$existing_products[ $product_slug ] = $existing;
				}
			}
		} else {
			if ( $dry_run ) {
				$post_id = 0;
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
		}

		if ( $existing ) {
			$matched_product_ids[ $post_id ] = true;
		}

		if ( $existing && '' !== $row_type_slug ) {
			$current_type_slugs = wp_get_post_terms(
				$post_id,
				'product_category',
				array(
					'fields' => 'slugs',
				)
			);

			if ( is_wp_error( $current_type_slugs ) ) {
				$current_type_slugs = array();
			}

			sort( $current_type_slugs );
			$normalized_current_type = array_map( 'sanitize_title', $current_type_slugs );

			if ( array( $row_type_slug ) !== $normalized_current_type ) {
				$action    = 'update';
				$changes[] = sprintf(
					'Type: %1$s -> %2$s',
					empty( $normalized_current_type ) ? '[empty]' : implode( ', ', $normalized_current_type ),
					$row_type_slug
				);
			}
		} elseif ( ! $existing && '' !== $row_type_slug ) {
			$changes[] = sprintf( 'Type: [empty] -> %s', $row_type_slug );
		}

		if ( '' !== $row_type_slug ) {
			$type_term = get_term_by( 'slug', $row_type_slug, 'product_category' );

			if ( ! $type_term || is_wp_error( $type_term ) ) {
				++$summary['skip'];

				if ( $dry_run ) {
					$dry_run_rows[] = array(
						'row_number'      => $row_number,
						'product_code'    => $product_code,
						'matched_product' => $existing ? lc_products_format_report_product_label( $existing ) : '',
						'action'          => 'skip',
						'changes'         => array(),
						'message'         => sprintf( 'Unknown Type slug "%s"', $row_type_slug ),
					);
				}

				continue;
			}

			if ( ! $dry_run && $type_term && ! is_wp_error( $type_term ) ) {
				wp_set_post_terms( $post_id, array( (int) $type_term->term_id ), 'product_category', false );
			}
		}

		$processed_fields = array();

		foreach ( $field_mapping as $csv_column => $acf_field ) {
			if ( isset( $processed_fields[ $acf_field ] ) ) {
				continue;
			}

			$processed_fields[ $acf_field ] = true;
			$raw_value                      = lc_products_get_row_raw_value( $header_map, $row, 'size_units' === $acf_field ? array( 'Size Units', 'Size Unit' ) : $csv_column );

			if ( '' !== $raw_value || isset( $header_map[ $csv_column ] ) || ( 'size_units' === $acf_field && ( isset( $header_map['Size Units'] ) || isset( $header_map['Size Unit'] ) ) ) ) {

				// Skip empty values.
				if ( '' === $raw_value ) {
					continue;
				}

				if ( lc_products_is_numeric_import_field( $acf_field ) && ! is_numeric( $raw_value ) ) {
					if ( $dry_run ) {
						$changes[] = sprintf(
							'%1$s: invalid numeric value "%2$s" (field would be skipped)',
							$csv_column,
							$raw_value
						);
					}

					continue;
				}

				$value = $row_field_values[ $acf_field ];

				if ( $existing ) {
					$current_value = lc_products_normalize_current_value( $acf_field, get_field( $acf_field, $post_id ) );

					if ( $current_value !== $value ) {
						$action    = 'update';
						$changes[] = sprintf(
							'%1$s: %2$s -> %3$s',
							$csv_column,
							'' === (string) $current_value ? '[empty]' : wp_strip_all_tags( (string) $current_value ),
							wp_strip_all_tags( (string) $value )
						);
					}
				}

				if ( ! $dry_run ) {
					update_field( $acf_field, $value, $post_id );
				}
			}
		}

		if ( 'create' === $action ) {
			++$summary['create'];
		} elseif ( 'update' === $action ) {
			++$summary['update'];
		} else {
			++$summary['unchanged'];
		}

		if ( $dry_run ) {
			$dry_run_rows[] = array(
				'row_number'      => $row_number,
				'product_code'    => $product_code,
				'matched_product' => $existing ? lc_products_format_report_product_label( $existing ) : '',
				'action'          => $action,
				'changes'         => $changes,
				'message'         => empty( $changes ) ? ( 'create' === $action ? 'New product would be created' : 'No mapped field changes detected' ) : '',
			);
		}

		++$imported;
	}

	fclose( $handle );

	$imported_product_slugs = array_unique( $imported_product_slugs );

	if ( 'replace' === $import_mode ) {
		foreach ( $existing_products as $existing_slug => $existing_product ) {
			if ( in_array( $existing_slug, $imported_product_slugs, true ) ) {
				continue;
			}

			++$summary['remove'];

			if ( $dry_run ) {
				$dry_run_rows[] = array(
					'row_number'      => '-',
					'product_code'    => $existing_product->post_title,
					'matched_product' => lc_products_format_report_product_label( $existing_product ),
					'action'          => 'remove',
					'changes'         => array(),
					'message'         => lc_products_get_missing_action_label( $missing_product_action ) . ( $remove_missing_images ? ' and remove featured image' : '' ),
				);
				continue;
			}

			lc_products_apply_missing_product_action( $existing_product->ID, $missing_product_action, $remove_missing_images );
		}
	}

	if ( $dry_run ) {
		$user_id = get_current_user_id();
		$key     = strtolower( wp_generate_password( 12, false, false ) );

		set_transient(
			'lc_product_dry_run_' . $user_id . '_' . $key,
			array(
				'summary' => $summary,
				'rows'    => $dry_run_rows,
			),
			15 * MINUTE_IN_SECONDS
		);

		wp_redirect(
			lc_products_import_page_url(
				array(
					'dry_run_report' => $key,
					'import_mode'    => $import_mode,
				)
			)
		);
		exit;
	}

	wp_redirect(
		lc_products_import_page_url(
			array(
				'imported'    => $imported,
				'processed'   => $summary['processed'],
				'created'     => $summary['create'],
				'updated'     => $summary['update'],
				'unchanged'   => $summary['unchanged'],
				'skipped'     => $summary['skip'],
				'removed'     => $summary['remove'],
				'import_mode' => $import_mode,
			)
		)
	);
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
	$selected_category     = isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0;
	$selected_image_filter = isset( $_GET['image_filter'] ) ? sanitize_key( wp_unslash( $_GET['image_filter'] ) ) : 'all';
	$selected_image_filter = in_array( $selected_image_filter, array( 'all', 'with', 'without' ), true ) ? $selected_image_filter : 'all';
	$search_term           = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';

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
		$meta_query  = new WP_Query( $meta_args );

		// Combine and deduplicate.
		$all_products = array_merge( $title_query->posts, $meta_query->posts );
		$product_ids  = array();
		$products     = array();

		foreach ( $all_products as $product ) {
			if ( ! in_array( $product->ID, $product_ids, true ) ) {
				$product_ids[] = $product->ID;
				$products[]    = $product;
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
		$query    = new WP_Query( $args );
		$products = $query->posts;
	}

	if ( 'with' === $selected_image_filter || 'without' === $selected_image_filter ) {
		$products = array_values(
			array_filter(
				$products,
				function ( $product ) use ( $selected_image_filter ) {
					$has_image = has_post_thumbnail( $product->ID );

					return 'with' === $selected_image_filter ? $has_image : ! $has_image;
				}
			)
		);
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

				<div>
					<label for="image_filter">Image: </label>
					<select name="image_filter" id="image_filter">
						<option value="all" <?php selected( $selected_image_filter, 'all' ); ?>>All Products</option>
						<option value="with" <?php selected( $selected_image_filter, 'with' ); ?>>With Image</option>
						<option value="without" <?php selected( $selected_image_filter, 'without' ); ?>>Without Image</option>
					</select>
				</div>
				
				<button type="submit" class="button">Filter</button>
				
				<?php if ( $search_term || $selected_category || 'all' !== $selected_image_filter ) : ?>
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

	$image_id    = isset( $_POST['featured_image_id'] ) ? intval( $_POST['featured_image_id'] ) : 0;
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
