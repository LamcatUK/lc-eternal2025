<?php
/**
 * Block template for LC Button Bar.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- button_bar -->
<section class="button_bar mb-5">
	<div class="container-xl d-flex justify-content-center">
		<?php
		while ( have_rows( 'buttons' ) ) {
			the_row();
			$l     = get_sub_field( 'link' );
			$class = strtolower(get_sub_field('style'));
			echo '<div class="px-2"><a href="' . $l['url'] . '" target="' . $l['target'] . '" class="btn btn-' . $class . '">' . $l['title'] . '</a></div>';
		}
		?>
	</div>
</section>
