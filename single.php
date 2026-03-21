<?php
/**
 * Template for displaying single blog posts.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;
$img = get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'single-blog__image' ) );

add_action(
	'wp_head',
	function () {
		global $schema;
		echo esc_html( $schema );
	}
);

get_header();

?>
<main id="main" class="single-blog">
	<?php
	$content = get_the_content();
	$blocks  = parse_blocks( $content );
	$sidebar = array();
	$after;
	?>
	<section class="breadcrumbs container-xl pb-2">
		<?php
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' );
		}
		?>
	</section>
	<div class="container-xl">
		<div class="row g-4 pb-4">
			<div class="col-lg-9 order-2 order-lg-2">
				<h1 class="single-blog__title mb-3"><?= esc_html( get_the_title() ); ?></h1>
				<?= wp_kses_post( $img ); ?>
				<div class="single-blog__read mb-4">
					<div>
						<i class="far fa-calendar-alt"></i> <?= esc_html( get_the_date( 'j F, Y' ) ); ?>
					</div>
					<div>
						<i class="far fa-clock"></i> <?= wp_kses_post( estimate_reading_time_in_minutes( get_the_content(), 200, true, true ) ); ?>
					</div>
				</div>
				<?php
				foreach ( $blocks as $block ) {
					if ( 'core/heading' === $block['blockName'] ) {
						if ( ! array_key_exists( 'level', $block['attrs'] ) ) {
							$heading    = wp_strip_all_tags( $block['innerHTML'] );
							$heading_id = sanitize_title( $heading );
							echo '<a id="' . esc_attr( $heading_id ) . '" class="anchor"></a>';
							$sidebar[ $heading ] = $heading_id;
						}
					}
					// Render block markup directly; avoid 'the_content' filters which can inject stray <p> tags.
					echo render_block( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
				<nav class="post-navigation container-xl py-4" aria-label="Post Navigation">
					<div class="d-flex justify-content-between gap-3">
						<div class="prev">
							<?php
							$prev_post = get_previous_post();
							if ( $prev_post instanceof WP_Post ) {
								?>
								<a class="btn btn-secondary" href="<?= esc_url( get_permalink( $prev_post ) ); ?>">
									&larr; Previous
								</a>
								<?php
							}
							?>
						</div>
						<div class="next text-end">
							<?php
							$next_post = get_next_post();
							if ( $next_post instanceof WP_Post ) {
								?>
								<a class="btn btn-secondary" href="<?= esc_url( get_permalink( $next_post ) ); ?>">
									Next &rarr;
								</a>
								<?php
							}
							?>
						</div>
					</div>
				</nav>
			</div>
			<div class="col-lg-3 order-1 order-lg-1">
				<div class="sidebar-insights">
					<?php
					if ( $sidebar ) {
						?>
						<div class="quicklinks">
							<div class="h5 has-line d-none d-lg-inline-block">Quick Links</div>
							<button class="d-lg-none accordion-button collapsed h5" type="button" data-bs-toggle="collapse"
								data-bs-target="#links" aria-expanded="true" aria-controls="links">Quick Links</button>

							<!-- <div class="h5 d-lg-none" data-bs-toggle="collapse" href="#links" role="button">Quick Links</div> -->
							<div class="collapse d-lg-block" id="links">
								<ul class="pt-3 pt-lg-0">
									<?php
									foreach ( $sidebar as $heading => $heading_id ) {
										?>
										<li><a
												href="#<?= esc_attr( $heading_id ); ?>"><?= esc_html( $heading ); ?></a>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<?php
					}
					?>
					<div class="sidebar-insights__cta mt-3 d-none d-lg-block">
						<div class="d-flex gap-3 justify-content-center align-items-center phone-email">
							<a href="<?= esc_url( 'tel:' . parse_phone( get_field( 'phone', 'option' ) ) ); ?>" class="button button-primary"><i class="fas fa-phone"></i> Call</a>
							<a href="<?= esc_url( 'mailto:' . antispambot( get_field( 'email', 'option' ) ) ); ?>" class="button button-primary"><i class="fas fa-paper-plane"></i> Email</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

	$cats = get_the_category();
	$ids  = wp_list_pluck( $cats, 'term_id' );

	$q = new WP_Query(
		array(
			'post_type'      => 'post',
			'category__in'   => $ids,
			'posts_per_page' => 4,
			'post__not_in'   => array( get_the_ID() ),
		)
	);

	if ( $q->have_posts() ) {
		?>
		<section class="related mt-3 py-5 has-blue-50-background-color has-background-color">
			<div class="container-xl">
				<div class="d-flex justify-content-between mb-4 align-items-center">
					<h2>Related Eye Care Insights</h2>
					<a class="btn btn-secondary align-self-center"
						href="<?= esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
						View All Insights
					</a>
				</div>
				<div class="row">
					<?php
					while ( $q->have_posts() ) {
						$q->the_post();
						$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
						if ( ! $img ) {
							$img = get_stylesheet_directory_uri() . '/img/default-blog.jpg';
						}
						?>
					<div class="col-md-4">
						<a href="<?= esc_url( get_the_permalink() ); ?>"
							class="news_index__card h-100 gap-2">
							<div class="news_index__image">
								<img src="<?= esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>"
									alt="">
							</div>
							<div class="news_index__inner">
								<h3><?= esc_html( get_the_title() ); ?></h2>
								<div class="smallest has-blue-400-color mb-2 d-flex align-items-center gap-2">
									<span><i class="far fa-calendar-alt"></i> <?= esc_html( get_the_date( 'j F, Y' ) ); ?></span>
									<span><i class="far fa-clock"></i> <?= wp_kses_post( estimate_reading_time_in_minutes( get_the_content(), 200, true, false ) ); ?> min read</span>
								</div>
							</div>
						</a>
					</div>
						<?php
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	}
	?>
</main>
<?php
get_footer();
