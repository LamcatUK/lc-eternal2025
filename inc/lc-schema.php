<?php
/**
 * LC Schema Markup
 *
 * Copied from the original lc-iology theme to preserve LocalBusiness + opening hours schema.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

// Disable Yoast's organization schema completely.
add_filter( 'wpseo_schema_organization', '__return_false' );
add_filter( 'wpseo_schema_company_logo_id', '__return_false' );

/**
 * Clean Yoast schema for iology.
 *
 * Removes Yoast's Organization and Person pieces,
 * and rewrites all Yoast references so they point
 * to the Optician business entity (#business).
 */
add_filter(
	'wpseo_schema_graph_pieces',
	function ( $pieces, $context ) {

		foreach ( $pieces as $index => $piece ) {

			// Remove Yoast Organisation.
			if ( $piece instanceof \Yoast\WP\SEO\Generators\Schema\Organization ) {
				unset( $pieces[ $index ] );
				continue;
			}

			// Remove Yoast Person (author schema).
			if ( $piece instanceof \Yoast\WP\SEO\Generators\Schema\Person ) {
				unset( $pieces[ $index ] );
				continue;
			}

			// Remove WebPage schema on contact page.
			if ( is_page( 'contact' ) && $piece instanceof \Yoast\WP\SEO\Generators\Schema\WebPage ) {
				unset( $pieces[ $index ] );
				continue;
			}

			// Rewrite Yoast WebPage and WebSite references.
			if ( method_exists( $piece, 'context' ) ) {
				$context_data = $piece->context;

				// Replace Yoast's #organization ID with your #business ID.
				if ( isset( $context_data['id'] ) && 'https://iology.co.uk/#organization' === $context_data['id'] ) {
					$context_data['id'] = 'https://iology.co.uk/#business';
				}

				// Rewrite publisher.
				if ( isset( $context_data['publisher'] ) &&
					isset( $context_data['publisher']['@id'] ) &&
					'https://iology.co.uk/#organization' === $context_data['publisher']['@id']
				) {
					$context_data['publisher']['@id'] = 'https://iology.co.uk/#business';
				}

				// Rewrite about → #business.
				if ( isset( $context_data['about'] ) &&
					isset( $context_data['about']['@id'] ) &&
					'https://iology.co.uk/#organization' === $context_data['about']['@id']
				) {
					$context_data['about']['@id'] = 'https://iology.co.uk/#business';
				}

				// Push changes back into piece.
				$piece->context = $context_data;
			}
		}

		return $pieces;
	},
	20,
	2
);

/**
 * Output schema markup for the site.
 *
 * @return void
 */
function lc_output_schema() {
	if ( is_front_page() || is_home() ) {

		// ORGANIZATION SCHEMA.
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Optician',
			'@id'         => 'https://iology.co.uk/#business',
			'name'        => 'Iology',
			'url'         => 'https://iology.co.uk/',
			'image'       => 'https://iology.co.uk/wp-content/uploads/2023/02/iology-org.png',
			'logo'        => 'https://iology.co.uk/wp-content/uploads/2023/02/iology-org.png',
			'description' => 'Independent optician in Barking offering comprehensive eye tests, contact lenses, glasses, and family eye care.',
			'slogan'      => 'Your local, independent optician.',
			'telephone'   => '020 8594 2714',
			'priceRange'  => '£',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => '50 Ripple Road',
				'addressLocality' => 'Barking',
				'postalCode'      => 'IG11 7PG',
				'addressCountry'  => 'GB',
			),
			'geo'         => array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => 51.5364754,
				'longitude' => 0.0811518,
			),
			'hasMap'      => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2481.731990079789!2d0.07857687662182146!3d51.536475371819925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47d8a66d4f21a521%3A0xab6a5ee20e66e26a!2sIology!5e0!3m2!1sen!2suk!4v1764530194062!5m2!1sen!2suk',
			'sameAs'      => array(
				'https://www.instagram.com/iology1',
				'https://www.google.com/maps/place/?q=place_id:ChIJIaUhT22m2EcRauJmDuJeaqs',
			),
		);

		// Add opening hours from plugin if available.
		if ( function_exists( 'get_opening_hours_specification_array' ) ) {
			$opening_hours = get_opening_hours_specification_array();
			if ( ! empty( $opening_hours ) ) {
				$schema['openingHoursSpecification'] = $opening_hours;
			}
		}
		echo '<script type="application/ld+json">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		echo '</script>';

	}

	// Check for custom schema first (works on all pages including contact).
	$custom_schema = get_field( 'schema' );
	if ( ! empty( $custom_schema ) ) {
		// Decode to validate JSON, then re-encode for consistent output.
		$schema_data = json_decode( $custom_schema, true );
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $schema_data ) ) {
			echo '<script type="application/ld+json">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
			echo '</script>';
			return;
		}
	}

	// Fallback contact page schema if no custom schema defined.
	if ( is_page( 'contact' ) ) {
		$contact_schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'ContactPage',
			'name'     => 'Contact Iology',
			'url'      => get_permalink(),
			'about'    => array(
				'@id' => 'https://iology.co.uk/#business',
			),
		);
		echo '<script type="application/ld+json">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_json_encode( $contact_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		echo '</script>';
	}
}
