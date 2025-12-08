<?php
/**
 * The header for the theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package lc-eternal2025
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta
		charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="preload"
		href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/Satoshi-Black.woff2' ); ?>"
		as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload"
		href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/Satoshi-Bold.woff2' ); ?>"
		as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload"
		href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/Satoshi-Light.woff2' ); ?>"
		as="font" type="font/woff2" crossorigin="anonymous">
	<?php

	lc_output_schema();

	if ( get_field( 'ga_property', 'options' ) ) {
		?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async
		src="https://www.googletagmanager.com/gtag/js?id=<?= esc_attr( get_field( 'ga_property', 'options' ) ); ?>">
	</script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());
		gtag('config',
			'<?= esc_attr( get_field( 'ga_property', 'options' ) ); ?>'
		);
	</script>
		<?php
	}
	if ( get_field( 'gtm_property', 'options' ) ) {
		?>
	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer',
			'<?= esc_attr( get_field( 'gtm_property', 'options' ) ); ?>'
		);
	</script>
	<!-- End Google Tag Manager -->
		<?php
	}
	if ( get_field( 'google_site_verification', 'options' ) ) {
		echo '<meta name="google-site-verification" content="' . esc_attr( get_field( 'google_site_verification', 'options' ) ) . '" />';
	}
	if ( get_field( 'bing_site_verification', 'options' ) ) {
		echo '<meta name="msvalidate.01" content="' . esc_attr( get_field( 'bing_site_verification', 'options' ) ) . '" />';
	}

	wp_head();
	?>
</head>

<body <?php body_class(); ?>>
	<?php
	do_action( 'wp_body_open' );
	?>
	<?php
	if ( ! is_user_logged_in() ) {
		if ( get_field( 'gtm_property', 'options' ) ) {
			?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe
		src="https://www.googletagmanager.com/ns.html?id=<?= esc_attr( get_field( 'gtm_property', 'options' ) ); ?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
			<?php
		}
	}
	?>
	<a class="skip-link screen-reader-text" href="#main" style="display:none">Skip to content</a>
	<header id="wrapper-navbar" class="fixed-top">
		<nav id="main-nav" class="navbar navbar-expand-lg py-2" aria-labelledby="main-nav-label">
			<div class="container">
				<div class="d-flex justify-content-between w-100 w-lg-auto align-items-center py-0">
					<!-- Logo -->
					<a href="/" class="logo"></a>

					<!-- Mobile Menu Toggle -->
					<button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
						data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
						aria-label="Toggle navigation">
						<i class="fa-solid fa-bars"></i>
					</button>
				</div>

				<!-- Navbar Content -->
				<div id="navbarContent" class="collapse navbar-collapse">
					<div class="w-100 d-flex flex-column justify-content-lg-between align-items-lg-center row-gap-2">
						<!-- Contact Details (Hidden on Mobile) -->
						<div class="contact-info d-none d-lg-flex gap-3 w-100 justify-content-end align-items-center pb-2">
							<img src="<?= esc_url( get_stylesheet_directory_uri() ); ?>/img/gb.svg" alt="GB" class="header_flag" width=18 height=18>
							<span><i class="fas fa-phone-alt has-amber-400-color"></i> <?= do_shortcode( '[contact_phone]' ); ?></span>
							<span><i class="fas fa-envelope has-amber-400-color"></i> <?= do_shortcode( '[contact_email]' ); ?></span>
						</div>

						<!-- Navigation -->
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary_nav',
								'container'      => false,
								'menu_class'     => 'navbar-nav w-100 justify-content-end flex-wrap align-items-lg-center',
								'fallback_cb'    => '',
								'depth'          => 3,
								'walker'         => new Understrap_WP_Bootstrap_Navwalker(),
							)
						);
						?>
					</div>
				</div>
			</div>
		</nav>
	</header>