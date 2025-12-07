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
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/montserrat-v25-latin-600.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/montserrat-v25-latin-800.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/montserrat-v25-latin-regular.woff2' ); ?>"
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
    <a class="skip-link screen-reader-text" href="#main" style="display:none">Skip to content</a>
    <?php
    do_action( 'wp_body_open' );
    ?>
    <header id="wrapper-navbar" class="fixed-top p-0">
        <nav id="navbar" class="navbar navbar-expand-xl d-block py-0" aria-label="Main navigation">
            <div id="top-nav">
                <div class="container-xl d-flex align-items-center justify-content-between">
                    <a href="/" class="navbar-brand logo-link logo" rel="home" aria-label="Iology Homepage"></a>
                    <div class="d-none d-xl-flex navbar-nav justify-content-end my-2 w-100 align-content-end">
                        <div class="contacts d-flex justify-content-between me-4 w-100">
                            <a href="/book-appointment/" class="nav-link"><i class="fa-regular fa-calendar"></i>
                                Book an appointment</a>
                            <a href="/contact/" class="nav-link"><i class="fa-solid fa-map-marker-alt"></i>
                                Find us</a>
                            <a href="tel:<?= esc_attr( parse_phone( get_field( 'phone', 'options' ) ) ); ?>"
                                class="nav-link"><i class="fa-solid fa-phone"></i>
                                Call us on
                                <?= esc_html( get_field( 'phone', 'options' ) ); ?></a>
                            <a href="mailto:<?= esc_attr( get_field( 'email', 'options' ) ); ?>"
                                class="nav-link"><i class="fa-regular fa-envelope"></i>
                                email
                                <?= esc_html( get_field( 'email', 'options' ) ); ?></a>
                        </div>
                    </div>
                    <div class="d-flex d-xl-none align-self-center">
                        <button class="navbar-toggler collapsed align-self-end me-auto input-button" id="navToggle"
                            data-bs-toggle="collapse" data-bs-target="#primaryNav" type="button"
                            aria-label="Navigation Toggle">
                            <span class="navbar-icon"><i class="fa-solid fa-bars"></i></span>
                            <div class="close-icon py-1"><i class="fa-solid fa-times"></i></div>
                        </button>
                    </div>
                </div>
            </div>
            <div id="main-nav">
                <div class="container-xl">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location'  => 'primary_nav',
                            'container_class' => 'collapse navbar-collapse',
                            'container_id'    => 'primaryNav',
                            'menu_class'      => 'navbar-nav w-100 justify-content-between align-items-xl-center',
                            'fallback_cb'     => '',
                            'menu_id'         => 'main-menu',
                            'depth'           => 2,
                            'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
                        )
                    );
                    ?>
                </div>
            </div>
        </nav>
    </header>