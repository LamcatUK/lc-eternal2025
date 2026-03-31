<?php
/**
 * Buttons reference.
 *
 * @package lc-eternal2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 class="mb-3">Buttons</h2>
<div class="row g-3">
	<?php
	$buttons = array(
		array( 'btn btn-primary', 'Primary' ),
		array( 'btn btn-outline-primary', 'Outline' ),
		array( 'ep-button ep-button--primary', 'EP Primary' ),
		array( 'ep-button ep-button--secondary', 'EP Secondary' ),
	);
	foreach ( $buttons as [ $class, $label ] ) :
		?>
		<div class="col-md-6 col-xl-3">
			<div class="border rounded p-3 bg-white h-100 d-flex flex-column gap-2">
				<a class="<?= esc_attr( $class ); ?>" href="#"><?= esc_html( $label ); ?></a>
				<code><?= esc_html( $class ); ?></code>
			</div>
		</div>
		<?php
	endforeach;
	?>
</div>
