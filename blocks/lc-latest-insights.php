<?php
/**
 * Block template for LC Latest Insights.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="latest-insights py-5 has-blue-50-background-color has-background-color">
	<div class="container">
		<div class="d-flex justify-content-between mb-4 align-items-center">
			<h2>Latest Insights</h2>
			<a class="btn btn-secondary align-self-center"
				href="<?= esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
				View All Insights
			</a>
		</div>
		<div class="latest-insights__grid row g-4">
			<?php
			$latest_posts = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 3,
					'post_status'    => 'publish',
				)
			);
			if ( $latest_posts->have_posts() ) {
				while ( $latest_posts->have_posts() ) {
					$latest_posts->the_post();
					?>
					<div class="col-md-4">
						<a class="latest-insights__card"
							href="<?= esc_url( get_the_permalink( get_the_ID() ) ); ?>">
							<div class="latest-insights__card-image-container">
								<?= get_the_post_thumbnail( get_the_ID(), 'large' ); ?>
							</div>
							<div class="latest-insights__card-content">
								<div class="latest-insights__card-meta">
									<span class="latest-insights__card-date">
										<i class="far fa-calendar-alt"></i> <?= esc_html( get_the_date( 'j F, Y' ) ); ?>
									</span>
									<span class="latest-insights__card-time">
										<i class="far fa-clock"></i> <?= wp_kses_post( estimate_reading_time_in_minutes( get_the_content(), 200, true, false ) ); ?> min read
									</span>
								</div>
								<h3 class="latest-insights__card-title">
									<?= esc_html( get_the_title() ); ?>
								</h3>
							</div>
						</a>
					</div>
					<?php
				}
				wp_reset_postdata();
			}
			?>
		</div>
	</div>
</section>