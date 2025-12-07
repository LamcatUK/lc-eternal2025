<?php
/**
 * Block template for LC Blog CTA.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

// Support Gutenberg color picker.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
?>
<div class="lc-blog-cta <?= esc_attr( $bg . ' ' . $fg ); ?>">
	<div class="lc-blog-cta__content">
		<?php
		if ( get_field( 'title' ) ) {
			?>
			<h2 class="lc-blog-cta__title">
				<?php the_field( 'title' ); ?>
			</h2>
			<?php
		}
		if ( get_field( 'description' ) ) {
			?>
			<div class="lc-blog-cta__description mb-4">
				<?php the_field( 'description' ); ?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	$button = get_field( 'button' );
	if ( $button ) {
		$button_url    = $button['url'];
		$button_title  = $button['title'];
		$button_target = $button['target'] ? $button['target'] : '_self';
		?>
		<div class="lc-blog-cta__button">
			<a class="btn btn-primary"
				href="<?= esc_url( $button_url ); ?>"
				target="<?= esc_attr( $button_target ); ?>">
				<?= esc_html( $button_title ); ?>
			</a>
		</div>
		<?php
	}
	?>
</div>