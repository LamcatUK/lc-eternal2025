<?php
/**
 * Typography reference.
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vars = lc_theme_reference_get_css_vars();
	$families = array_filter( $vars, static fn( $value, $name ) => str_starts_with( $name, 'ff-' ), ARRAY_FILTER_USE_BOTH );
	$weights  = array_filter( $vars, static fn( $value, $name ) => str_starts_with( $name, 'fw-' ), ARRAY_FILTER_USE_BOTH );
	$sizes    = array_filter( $vars, static fn( $value, $name ) => str_starts_with( $name, 'fs-' ), ARRAY_FILTER_USE_BOTH );
ksort( $families );
ksort( $weights );
ksort( $sizes );
?>
<h2 class="mb-3">Typography</h2>
<div class="row g-3 mb-4">
	<?php foreach ( $families as $name => $value ) : ?>
		<div class="col-md-6 col-xl-4">
			<div class="border rounded p-3 bg-white h-100">
				<strong>--<?= esc_html( $name ); ?></strong>
				<code class="d-block mb-2"><?= esc_html( $value ); ?></code>
				<p style="font-family: var(--<?= esc_attr( $name ); ?>);">The quick brown fox jumps over the lazy dog.</p>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<div class="row g-3 mb-4">
	<?php foreach ( $weights as $name => $value ) : ?>
		<div class="col-md-6 col-xl-4">
			<div class="border rounded p-3 bg-white h-100">
				<strong>--<?= esc_html( $name ); ?></strong>
				<code class="d-block mb-2"><?= esc_html( $value ); ?></code>
				<p style="font-weight: var(--<?= esc_attr( $name ); ?>);">Weight sample</p>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<div class="row g-3">
	<?php foreach ( $sizes as $name => $value ) : ?>
		<div class="col-md-6 col-xl-4">
			<div class="border rounded p-3 bg-white h-100">
				<strong>--<?= esc_html( $name ); ?></strong>
				<code class="d-block mb-2"><?= esc_html( $value ); ?></code>
				<p style="font-size: var(--<?= esc_attr( $name ); ?>);">Size sample</p>
			</div>
		</div>
	<?php endforeach; ?>
</div>
