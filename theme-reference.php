<?php
/**
 * Template Name: Theme Reference
 *
 * Visual reference page for theme tokens, components, and blocks.
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$sections = array(
	'colours'    => 'Colours',
	'typography' => 'Typography',
	'buttons'    => 'Buttons',
	'grid'       => 'Grid',
	'forms'      => 'Forms',
	'blocks'     => 'Blocks',
);
?>
<main class="theme-reference py-5">
	<div class="container-xl">
		<h1 class="mb-4">Theme Reference</h1>
		<p class="lead mb-4">A quick audit page for LC Eternal theme tokens and components.</p>

		<div class="d-flex flex-wrap gap-2 mb-4">
			<?php foreach ( $sections as $anchor => $label ) : ?>
				<a class="btn btn-outline-primary btn-sm" href="#<?= esc_attr( $anchor ); ?>"><?= esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>

		<hr>
		<section id="colours" class="py-4">
			<?php require locate_template( 'reference-parts/colours.php' ); ?>
		</section>
		<hr>
		<section id="typography" class="py-4">
			<?php require locate_template( 'reference-parts/typography.php' ); ?>
		</section>
		<hr>
		<section id="buttons" class="py-4">
			<?php require locate_template( 'reference-parts/buttons.php' ); ?>
		</section>
		<hr>
		<section id="grid" class="py-4">
			<?php require locate_template( 'reference-parts/grid.php' ); ?>
		</section>
		<hr>
		<section id="forms" class="py-4">
			<?php require locate_template( 'reference-parts/forms.php' ); ?>
		</section>
		<hr>
		<section id="blocks" class="py-4">
			<?php require locate_template( 'reference-parts/blocks.php' ); ?>
		</section>
	</div>
</main>
<?php
get_footer();

/**
 * Get CSS custom properties from the compiled theme stylesheet.
 *
 * @return array<string, string>
 */
function lc_theme_reference_get_css_vars() {
	static $vars = null;

	if ( null !== $vars ) {
		return $vars;
	}

	$vars = array();
	$css_file = get_stylesheet_directory() . '/css/child-theme.css';

	if ( ! file_exists( $css_file ) ) {
		return $vars;
	}

	$css = file_get_contents( $css_file );
	if ( ! $css ) {
		return $vars;
	}

	preg_match_all( '/--(?<name>[a-z0-9\-_]+):\s*(?<value>[^;]+);/i', $css, $matches, PREG_SET_ORDER );

	foreach ( $matches as $match ) {
		$vars[ trim( $match['name'] ) ] = trim( $match['value'] );
	}

	ksort( $vars );

	return $vars;
}

/**
 * Extract a short description from a block template docblock.
 *
 * @param string $template_path Template path.
 * @return string
 */
function lc_theme_reference_get_template_description( $template_path ) {
	if ( ! file_exists( $template_path ) ) {
		return '';
	}

	$contents = file_get_contents( $template_path );
	if ( ! $contents || ! preg_match( '/\/\*\*(.*?)\*\//s', $contents, $docblock ) ) {
		return '';
	}

	$lines = preg_split( '/\R/', trim( $docblock[1] ) );
	$desc  = array();

	foreach ( $lines as $line ) {
		$line = preg_replace( '/^\s*\*\s?/', '', $line );
		if ( '' === $line || str_starts_with( $line, '@' ) ) {
			break;
		}
		$desc[] = $line;
	}

	return trim( implode( ' ', $desc ) );
}
