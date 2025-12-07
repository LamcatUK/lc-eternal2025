<?php
/**
 * Hero block template.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

// Get banner settings from options.
$banner_link        = get_field( 'banner_link', 'option' );
$banner_url         = $banner_link['url'] ?? '';
$show_banner_option = get_field( 'show_banner', 'option' );
$is_banner_page     = $banner_url && is_page( basename( $banner_url ) );

// Add margin if we're on the banner page (banner won't show).
$margin = $is_banner_page ? 'mb-5' : '';

?>
<!-- hero -->
<section class="hero <?= esc_attr( $margin ); ?>"
	style="background-image:url(<?= esc_url( wp_get_attachment_image_url( get_field( 'background' ), 'full' ) ); ?>)">
	<div class="container-xl">
		<div class="row mx-0">
			<div class="col-md-7 my-auto">
				<h1><?= esc_html( get_field( 'title' ) ); ?></h1>
				<h2 class="mb-4">
					<?= esc_html( get_field( 'subtitle' ) ); ?>
					</h2>
					<?php
					if ( get_field( 'cta1' ) ) {
						$c     = get_field( 'cta1' );
						$style = get_field( 'cta1_primary' ) ? 'btn-primary' : 'btn-secondary';
						?>
					<a href="<?= esc_url( $c['url'] ); ?>"
						target="<?= esc_attr( $c['target'] ); ?>"
						class="btn <?= esc_attr( $style ); ?> me-2 mb-2"><?= esc_html( $c['title'] ); ?></a>
						<?php
					}
					if ( get_field( 'cta2' ) ) {
						$style = get_field( 'cta2_primary' ) ? 'btn-primary' : 'btn-secondary';
						$c     = get_field( 'cta2' );
						?>
					<a href="<?= esc_url( $c['url'] ); ?>"
						target="<?= esc_attr( $c['target'] ); ?>"
						class="btn <?= esc_attr( $style ); ?> mb-2"><?= esc_html( $c['title'] ); ?></a>
						<?php
					}
					?>
			</div>
		</div>
	</div>
</section>
<?php

// Show banner if enabled and not on the banner page.
if ( ! empty( $show_banner_option[0] ) && 'Yes' === $show_banner_option[0] && ! $is_banner_page ) {
	?>
<!-- banner -->
<section class="banner py-1 mb-5">
	<a href="<?= esc_url( $banner_url ? $banner_url : '#' ); ?>">
		<div class="container-xl text-center d-md-flex justify-content-center align-items-center">
			<div class="banner__content">
				<?= wp_kses_post( get_field( 'banner_text', 'option' ) ); ?>
			</div>
		</div>
	</a>
</section>
	<?php
}
?>
