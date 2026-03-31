<?php
/**
 * Theme colour reference.
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vars = lc_theme_reference_get_css_vars();
$colours = array_filter(
	$vars,
	static function ( $value, $name ) {
		return str_starts_with( $name, 'col-' );
	},
	ARRAY_FILTER_USE_BOTH
);
ksort( $colours );
?>
<h2 class="mb-3">Colours</h2>
<div class="row g-3">
	<?php foreach ( $colours as $name => $value ) : ?>
		<div class="col-sm-6 col-lg-4 col-xl-3">
			<div class="border rounded p-3 h-100 bg-white">
				<div class="rounded mb-2" style="height:64px;background:var(--<?= esc_attr( $name ); ?>);"></div>
				<strong>--<?= esc_html( $name ); ?></strong>
				<code class="d-block"><?= esc_html( $value ); ?></code>
			</div>
		</div>
	<?php endforeach; ?>
</div>
