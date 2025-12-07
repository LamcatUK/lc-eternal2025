<?php
/**
 * Block template for LC Brand Grid.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="brand-grid mb-5">
	<div class="container-xl">
		<div class="row g-4 justify-content-center">
			<?php
			while ( have_rows( 'brands' ) ) {
				the_row();
				?>
			<div class="col-4 col-md-3 col-lg-2">
				<?= wp_get_attachment_image( get_sub_field( 'brand_logo' ), 'full' ); ?>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
