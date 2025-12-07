<?php
/**
 * LC Form Block Template.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- form -->
<a id="form" class="anchor"></a>
<section class="frm py-5">
	<div class="container-xl">
		<div class="row">
			<div class="col-lg-4">
				<h2><?= esc_html( get_field( 'title' ) ); ?></h2>
				<p><?= wp_kses_post( get_field( 'content' ) ); ?></p>
			</div>
			<div class="col-lg-8">
				<div class="frm__card">
					<?= do_shortcode( '[contact-form-7 id="' . esc_attr( get_field( 'form_id' ) ) . '" subject="' . esc_attr( get_field( 'subject' ) ) . '"]' ); ?>
				</div>
			</div>
		</div>
	</div>
</section>
