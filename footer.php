<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package lc-eternal2025
 */

defined( 'ABSPATH' ) || exit;

?>
	<footer class="footer pt-5">
		<div class="container">
			<div class="row g-sm-4 g-lg-2 g-xxl-5">
				<div class="col-xl-3 mb-3 pe-3 border-end">
					<img src="<?= esc_url( get_stylesheet_directory_uri() ); ?>/img/ep-logo-2025.svg" alt="Eternal Packaging Ltd" class="footer__logo" width="200" height="68">
					<img src="<?= esc_url( get_stylesheet_directory_uri() ); ?>/img/brcgs_cert_packaging_logo_rgb.webp" alt="BRCGS Certification" class="footer__brcgs">
				</div>
				<div class="col-sm-6 col-xl-3 border-end">
					<ul class="fa-ul">
						<li class="mb-2">
							<span class="fa-li">
								<i class="fas fa-map-marker-alt"></i>
							</span>
							<?= wp_kses_post( get_field( 'contact_address', 'options' ) ); ?>
						</li>
						<li class="mb-2">
							<span class="fa-li">
								<i class="fas fa-phone"></i>
							</span>
							<a href="tel:<?= esc_html( get_field( 'contact_phone', 'options' ) ); ?>"><?= esc_html( get_field( 'contact_phone', 'options' ) ); ?></a>
						</li>
						<li>
							<span class="fa-li">
								<i class="fas fa-envelope"></i>
							</span>
							<a href="mailto:<?= esc_html( antispambot( get_field( 'contact_email', 'options' ) ) ); ?>"><?= esc_html( antispambot( get_field( 'contact_email', 'options' ) ) ); ?></a>
						</li>
						<li>
							<?= do_shortcode( '[social_icons prefix="Connect: "]' ); ?>
						</li>
					</ul>
				</div>
				<div class="col-sm-4 col-xl-2 border-end">
					<?=
					wp_nav_menu(
						array(
							'theme_location'  => 'footer_menu1',
							'container_class' => 'footer__menu',
						)
					);
					?>
				</div>
				<div class="col-sm-4 col-xl-2 border-end">
					<?=
					wp_nav_menu(
						array(
							'theme_location'  => 'footer_menu2',
							'container_class' => 'footer__menu',
						)
					);
					?>
				</div>
				<div class="mb-4 mb-sm-0 col-sm-4 col-xl-2">
					<?=
					wp_nav_menu(
						array(
							'theme_location'  => 'footer_menu3',
							'container_class' => 'footer__menu',
						)
					);
					?>
				</div>
			</div>

			<div class="row gx-2 colophon py-2">
				<div class="col-lg-7 text-center text-lg-start">
					&copy; <?= esc_html( gmdate( 'Y' ) ); ?> Eternal Packaging Ltd. Registered in England, no. 11702832.
				</div>
				<div class="col-lg-5 text-end d-flex gap-2 justify-content-center justify-content-lg-end flex-wrap">
					<a href="/terms/">Terms</a>
					|
					<span>
					<a href="/privacy-policy/">Privacy</a> &amp;
					<a href="/cookies/">Cookies</a>
					</span>
					|
					<span>Site by <a href="https://www.lamcat.co.uk/" rel="noopener" target="_blank" class="lc" title="Lamcat Design & Consulting">Lamcat</a></span>
				</div>
			</div>
		</div>
	</footer>
	<?php
	wp_footer();
	?>
</body>

</html>
