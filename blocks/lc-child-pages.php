<?php
/**
 * Block template for LC Child Pages.
 *
 * Outputs a linked list of child pages for the current page.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$page_id = get_the_ID();

if ( ! $page_id || 'page' !== get_post_type( $page_id ) ) {
	return;
}

$child_pages = get_pages(
	array(
		'child_of'    => $page_id,
		'parent'      => $page_id,
		'sort_column' => 'menu_order,post_title',
		'sort_order'  => 'ASC',
	)
);

if ( empty( $child_pages ) ) {
	return;
}

$class_name = $block['className'] ?? '';
$anchor     = $block['anchor'] ?? '';
$classes    = trim( 'lc-child-pages py-5 ' . $class_name );
?>
<section class="<?= esc_attr( $classes ); ?>"<?= $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
	<div class="container">
		<nav class="lc-child-pages__inner" aria-label="Child pages navigation">
			<ul class="lc-child-pages__list">
				<?php foreach ( $child_pages as $child_page ) : ?>
					<li class="lc-child-pages__item">
						<a class="lc-child-pages__link" href="<?= esc_url( get_permalink( $child_page->ID ) ); ?>">
							<span class="lc-child-pages__title"><?= esc_html( get_the_title( $child_page->ID ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	</div>
</section>
