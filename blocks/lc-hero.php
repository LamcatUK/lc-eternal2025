<?php
/**
 * Block template for LC Hero.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$images        = get_field( 'images' );
$heading       = get_field( 'heading' );
$strapline     = get_field( 'strapline' );
$button_link_1 = get_field( 'button_1' );
$button_link_2 = get_field( 'button_2' );

$size       = get_field( 'size' );
$size_class = ( 'Short' === $size ) ? 'hero--short' : '';

$block_id = 'lc-hero-' . $block['id'];
?>
<section class="hero <?= esc_attr( $size_class ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
	<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
		<div class="carousel-inner">
			<?php
			$active = 'active';
			$c      = false;
			if ( $images ) {
				foreach ( $images as $index => $image ) {
					?>
					<div class="carousel-item <?= esc_attr( $active ); ?>">
						<?=
						wp_get_attachment_image(
							$image['ID'],
							'full',
							false,
							array(
								'class' => 'd-block w-100 h-100',
							),
						);
						?>
					</div>
					<?php
					$active = '';
					$c      = true;
				}
			}
			if ( false === $c ) {
				?>
				<div class="carousel-item active">
					<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/default-hero.jpg' ); ?>" class="d-block w-100 h-100" alt="Hero Image">
				</div>
				<?php
			}
			?>
		</div>
	</div>

	<div class="hero__overlay"></div>

	<div class="hero__content d-flex align-items-center">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 text-white">
					<?php
					$d = 0;
					?>
					<h1 data-aos="fade" data-aos-delay="<?= esc_attr( $d ); ?>" class="hero__title"><?= esc_html( $heading ); ?></h1>
					<?php
					$d += 100;
					if ( $strapline ) {
						?>
					<h2 data-aos="fade" data-aos-delay="<?= esc_attr( $d ); ?>" class="hero__strapline"><?= esc_html( $strapline ); ?></h2>
						<?php
						$d += 100;
					}
					?>
			
					<div class="hero__buttons d-flex flex-wrap gap-2 align-items-center mt-4">
						<?php
						if ( $button_link_1 ) {
							?>
						<span data-aos="fade" data-aos-delay="<?= esc_attr( $d ); ?>">
							<a class="ep-button ep-button--primary hero__button mt-3"
								href="<?= esc_url( $button_link_1['url'] ); ?>"
								target="<?= esc_attr( $button_link_1['target'] ?? '_self' ); ?>">
								<?= esc_html( $button_link_1['title'] ); ?>
							</a>
						</span>
							<?php
							$d += 100;
						}
						if ( $button_link_2 ) {
							?>
						<span data-aos="fade" data-aos-delay="<?= esc_attr( $d ); ?>">
							<a class="ep-button ep-button--secondary ep-button--secondary-white hero__button mt-3"
								href="<?= esc_url( $button_link_2['url'] ); ?>"
								target="<?= esc_attr( $button_link_2['target'] ?? '_self' ); ?>">
								<?= esc_html( $button_link_2['title'] ); ?>
							</a>
						</span>
							<?php
							$d += 100;
						}
						?>
					</div>
					<?php
					if ( is_front_page() ) {
						echo '<img data-aos="fade" data-aos-delay="' . esc_attr( $d ) . '" src="' . esc_url( get_stylesheet_directory_uri() . '/img/brcgs_cert_packaging_logo_rgb.webp' ) . '" alt="BRCGS Certification" class="hero__brcgs" />';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
