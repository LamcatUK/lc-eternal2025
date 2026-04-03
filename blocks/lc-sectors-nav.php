<?php
/**
 * Block template for LC Sectors Nav.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

/* get ids of pages with parent 'sectors' */

$parent_page = get_page_by_path( 'sectors' );

$child_args = array(
	'post_parent' => $parent_page->ID,
	'post_type'   => 'page',
	'post_status' => 'publish',
);

$children = get_children( $child_args );

$background = get_field( 'background' );
$bgcolour   = $background ? $background : 'white';

?>
<section class="lc-sectors-nav lc-nav-cards py-5 bg--<?= esc_attr( $bgcolour ); ?>">
	<div class="container">
		<h2 class="has-white-color mb-4"><?= esc_html( get_field( 'title' ) ); ?></h2>
		<div class="has-white-color mb-5 larger"><?= wp_kses_post( get_field( 'content' ) ); ?></div>
		<div class="row g-4">
			<?php
			foreach ( $children as $child ) {
				$sector_link  = get_permalink( $child->ID );
				$sector_title = get_the_title( $child->ID );
				?>
			<div class="col-md-4">
				<a class="lc-nav-cards__link" href="<?= esc_url( $sector_link ); ?>">
					<div class="lc-nav-cards__media">
						<?php if ( has_post_thumbnail( $child->ID ) ) : ?>
							<?= get_the_post_thumbnail( $child->ID, 'large' ); ?>
						<?php else : ?>
							<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-product.jpg' ); ?>" alt="<?= esc_attr( $sector_title ); ?>">
						<?php endif; ?>
						<div class="lc-nav-cards__overlay"></div>
					</div>
					<div class="lc-nav-cards__title-wrap">
						<h3><?= esc_html( $sector_title ); ?></h3>
					</div>
				</a>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
