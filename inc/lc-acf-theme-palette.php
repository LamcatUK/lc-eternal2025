<?php
/**
 * Integrate ACF color picker fields with the theme's color palette.
 *
 * @package lc-eternal2025
 */

// Field name: background.
add_filter(
	'acf/load_field/name=background',
	function ( $field ) {
		// Prefer the theme palette; fall back to default/global if needed.
		$palette = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
		if ( empty( $palette ) ) {
			$palette = wp_get_global_settings( array( 'color', 'palette', 'default' ) );
		}

		$field['choices']    = array();
		$field['allow_null'] = 1;

		// Manually add white and black since WordPress filters them out.
		$field['choices']['white'] = 'White';
		$field['choices']['black'] = 'Black';

		foreach ( (array) $palette as $c ) {
			if ( empty( $c['slug'] ) || empty( $c['name'] ) ) {
				continue;
			}
			$field['choices'][ $c['slug'] ] = $c['name'];
		}

		return $field;
	}
);

// Field name: cta_colour.
add_filter(
	'acf/load_field/name=cta_colour',
	function ( $field ) {
		// Prefer the theme palette; fall back to default/global if needed.
		$palette = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
		if ( empty( $palette ) ) {
			$palette = wp_get_global_settings( array( 'color', 'palette', 'default' ) );
		}

		$field['choices']    = array();
		$field['allow_null'] = 1;

		// Manually add white and black since WordPress filters them out.
		$field['choices']['white'] = 'White';
		$field['choices']['black'] = 'Black';

		foreach ( (array) $palette as $c ) {
			if ( empty( $c['slug'] ) || empty( $c['name'] ) ) {
				continue;
			}
			$field['choices'][ $c['slug'] ] = $c['name'];
		}
		return $field;
	}
);
