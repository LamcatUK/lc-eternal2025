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
<div class="footer">
    <div class="container pt-4 pb-2">
        <div class="row g-4">
            <div class=" col-lg-3 mb-3">
                <div class="text-center text-md-start mb-3">
                    <img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/iology-logo--wo.svg' ); ?>"
                        class="logo" width="93" height="32" alt="iology Limited">
                </div>
                Iology incorporating Aves Opticians<br>
                50 Ripple Road, Barking,<br>
                England, IG11 7PG
            </div>
            <div class="col-md-6 col-lg-3">
                <?= wp_nav_menu( array( 'theme_location' => 'footer_menu1' ) ); ?>
            </div>
            <div class="col-md-6 col-lg-3">
                <?= wp_nav_menu( array( 'theme_location' => 'footer_menu2' ) ); ?>
            </div>
            <div class="col-lg-3">
                <ul class="fa-ul">
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-envelope"></i></span>
                        <a
                            href="mailto:<?= esc_attr( antispambot( get_field( 'email', 'options' ) ) ); ?>"><?= esc_html( antispambot( get_field( 'email', 'options' ) ) ); ?></a>
                    </li>
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-phone"></i></span>
                        <a
                            href="tel:<?= esc_attr( parse_phone( get_field( 'phone', 'options' ) ) ); ?>"><?= esc_html( get_field( 'phone', 'options' ) ); ?></a>
                    </li>
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-mobile-screen"></i></span>
                        <a
                            href="tel:<?= esc_attr( parse_phone( get_field( 'mobile', 'options' ) ) ); ?>"><?= esc_html( get_field( 'mobile', 'options' ) ); ?></a>
                    </li>
                </ul>
                <div class="social">
                    <?php /* social_icons() */ ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="colophon">
    <div class="container d-flex justify-content-between flex-wrap pb-2 pt-2">
        <div class="text-center mx-auto mb-4 mb-lg-0 ms-lg-0">&copy;
            <?= esc_html( gmdate( 'Y' ) ); ?> Eyes London Barking
            Ltd t/a iology incorporating Aves Opticians
            Limited. Registered in England no. 11860174
        </div>
        <div class="text-center mx-auto me-lg-0">
            <a href="/privacy-policy/">Privacy</a> &amp; <a
                href="/cookies/">Cookies</a> | Site by <a href="https://www.lamcat.co.uk/" target="_blank">Lamcat</a>
        </div>
    </div>
</div>
</div><!-- #page -->
<?php
wp_footer();
if ( get_field( 'gtm_property', 'options' ) ) {
    ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe
        src="https://www.googletagmanager.com/ns.html?id=<?= esc_attr( get_field( 'gtm_property', 'options' ) ); ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <?php
}
?>
</body>

</html>
