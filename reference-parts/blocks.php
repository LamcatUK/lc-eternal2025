<?php
/**
 * Blocks reference.
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$blocks = function_exists( 'acf_get_block_types' ) ? acf_get_block_types() : array();
?>
<h2 class="mb-3">Blocks</h2>
<p>Registered ACF blocks, their templates, and docblock summaries.</p>

<?php if ( ! empty( $blocks ) ) : ?>
	<div class="table-responsive">
		<table class="table table-striped align-middle bg-white">
			<thead>
				<tr>
					<th>Title</th>
					<th>Name</th>
					<th>Template</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $blocks as $block ) : ?>
					<?php
					$template = ! empty( $block['render_template'] ) ? locate_template( $block['render_template'] ) : '';
					?>
					<tr>
						<td><?= esc_html( $block['title'] ?? '' ); ?></td>
						<td><code><?= esc_html( $block['name'] ?? '' ); ?></code></td>
						<td><code><?= esc_html( $block['render_template'] ?? '' ); ?></code></td>
						<td><?= esc_html( $template ? lc_theme_reference_get_template_description( $template ) : 'No template found.' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<p>No ACF blocks registered.</p>
<?php endif; ?>
