<?php
/**
 * Block template for LC Contact Map.
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- contact_map -->
<section class="contact_map pt-5 pb-4">
	<div class="container-xl">
		<h2>Find Us</h2>
		<div class="row g-4">
			<div class="col-md-5">
				<div class="mb-4"><strong>iology</strong> is located at 50 Ripple Road, a short walk from Barking station and several local bus routes.</div>
				<div class="mb-4">Walk-ins are accepted when available, though appointments are recommended during busy periods.</div>
				<div class="open card mb-4">
					<h3><i class="far fa-clock"></i> Opening Hours</h3>
					<?= do_shortcode( '[lc_open_short_ajax]' ); ?>
				</div>
				<div class="contact card">
					<ul class="fa-ul mb-2">
						<li class="mb-2">
							<span class="fa-li"><i class="fa-solid fa-map-marker-alt"></i></span>
							<?= wp_kses_post( get_field( 'address', 'options' ) ); ?>
						</li>
						<li class="mb-4">
							<span class="fa-li"><i class="fa-regular fa-compass"></i></span>
							<a href="<?= esc_url( get_field( 'directions', 'options' ) ); ?>"
								target="_blank">Get Directions</a>
						</li>
					</ul>
					<h4>Ways to get in touch</h4>
					<ul class="fa-ul">
						<li class="mb-1">
							<span class="fa-li"><i class="fa-solid fa-phone"></i></span>
							<a
								href="tel:<?= esc_attr( parse_phone( get_field( 'phone', 'options' ) ) ); ?>"><strong><?= esc_html( get_field( 'phone', 'options' ) ); ?></strong></a>
						</li>
						<li class="mb-1">
							<span class="fa-li"><i class="fa-solid fa-mobile-screen"></i></span>
							<a
								href="tel:<?= esc_attr( parse_phone( get_field( 'mobile', 'options' ) ) ); ?>"><strong><?= esc_html( get_field( 'mobile', 'options' ) ); ?></strong></a>
						</li>
						<li class="mb-1">
							<span class="fa-li"><i class="fa-solid fa-envelope"></i></span>
							<a
								href="mailto:<?= esc_attr( get_field( 'email', 'options' ) ); ?>"><strong><?= esc_html( get_field( 'email', 'options' ) ); ?></strong></a>
						</li>
						<li>
							<span class="fa-li"><i class="fa-brands fa-whatsapp"></i></span>
							<a target="_blank"
								href="https://api.whatsapp.com/send?phone=<?= esc_attr( parse_phone( get_field( 'mobile', 'options' ) ) ); ?>&text=<?= esc_attr( "Hi, I'm contacting you from the iology website." ); ?>">
								WhatsApp</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-7">
				<iframe class="card map p-0" title="Map"
					src="<?= esc_url( get_field( 'maps_url', 'options' ) ); ?>"
					width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		</div>
	</div>
</section>
