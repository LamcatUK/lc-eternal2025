<?php
/**
 * Three Cards Block Template.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

$class_name = $block['className'] ?? 'mt-5';

// Count how many cards are defined.
$card_count = 0;
for ( $i = 1; $i <= 5; $i++ ) {
	if ( get_field( 'card_' . $i . '_link' ) ) {
		++$card_count;
	}
}

// Determine card size based on count.
$card_sizes = array(
	1 => 'col-md-6',
	2 => 'col-md-6',
	3 => 'col-md-4',
	4 => 'col-md-3',
	5 => 'col-md-2',
);
$card_size  = $card_sizes[ $card_count ] ?? 'col-md-4';

?>
<!-- cards -->
<section class="three_cards py-5 <?= esc_attr( $class_name ); ?>">
	<div class="container-xl">
		<div class="row g-4 justify-content-center">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) {
				$link = get_field( 'card_' . $i . '_link' );
				if ( ! $link ) {
					continue;
				}

				$icon  = wp_get_attachment_image_url( get_field( 'card_' . $i . '_icon' ), 'large' );
				$title = get_field( 'card_' . $i . '_title' );
				$body  = get_field( 'card_' . $i . '_body' );

				// For 5 cards: first 3 are col-md-4, last 2 are col-md-6.
				if ( 5 === $card_count ) {
					$current_card_size = ( $i <= 3 ) ? 'col-md-4' : 'col-md-6';
				} else {
					$current_card_size = $card_size;
				}
				?>
			<div class="<?= esc_attr( $current_card_size ); ?>">
				<div class="card">
					<div class="card__icon">
						<img src="<?= esc_url( $icon ); ?>" alt="<?= esc_attr( $title ); ?>" width="267" height="150">
					</div>
					<div class="card__bottom">
						<h3><?= esc_html( $title ); ?></h3>
						<p><?= esc_html( $body ); ?></p>
					</div>
					<div class="card__button">
						<a href="<?= esc_url( $link['url'] ); ?>"
							target="<?= esc_attr( $link['target'] ); ?>"
							class="btn btn-primary"><?= esc_html( $link['title'] ); ?></a>
					</div>
				</div>
			</div>
				<?php
			}
			?>
		</div>
		<?php
		if ( get_field( 'after_text' ) ) {
			?>
		<div class="text-center mt-5">
			<?= esc_html( get_field( 'after_text' ) ); ?>
		</div>
			<?php
		}
		if ( get_field( 'after_cta' ) ) {
			$c = get_field( 'after_cta' );
			?>
		<div class="text-center mt-4">
			<a href="<?= esc_url( $c['url'] ); ?>"
				target="<?= esc_attr( $c['target'] ); ?>"
				class="btn btn-primary"><?= esc_html( $c['title'] ); ?></a>
		</div>
			<?php
		}
		?>
	</div>
</section>
