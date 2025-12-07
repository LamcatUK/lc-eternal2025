<?php
/**
 * Register ACF blocks for the lc-mindspace theme.
 *
 * This file defines and registers custom ACF blocks.
 *
 * @package lc-eternal2025
 */

/**
 * Register ACF blocks.
 *
 * @return void
 */
function acf_blocks() {
	if ( function_exists( 'acf_register_block_type' ) ) {

		// INSERT NEW BLOCKS HERE.

        acf_register_block_type(
            array(
                'name'            => 'lc_service_card_slider',
                'title'           => __( 'LC Service Card Slider' ),
                'category'        => 'layout',
                'icon'            => 'cover-image',
                'render_template' => 'blocks/lc-service-card-slider.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'      => false,
                    'anchor'    => true,
                    'className' => true,
                    'align'     => true,
                ),
            )
        );

		acf_register_block_type(
			array(
				'name'            => 'lc_latest_insights',
				'title'           => __( 'LC Latest Insights' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/lc-latest-insights.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_blog_cta',
				'title'           => __( 'LC Blog CTA' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/lc-blog-cta.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'text'       => true,
						'background' => true,
						'gradients'  => false,
					),

				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_hero',
				'title'           => 'LC Hero',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_three_cards',
				'title'           => 'LC Three Card Block',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-three-cards.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
				),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'lc_two_cols',
				'title'           => 'LC Two Column Block',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-two-cols.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_text_image',
				'title'           => 'LC Text/Image Block',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-text-image.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_banner',
				'title'           => 'LC Banner',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-banner.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_testimonials',
				'title'           => 'LC Testimonials',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-testimonials.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_form',
				'title'           => 'LC Form',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-form.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_faq',
				'title'           => 'LC FAQs',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-faq.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_button_bar',
				'title'           => 'LC Button Bar',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-button-bar.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_contact_map',
				'title'           => 'LC Contact & Map',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'render_template' => 'blocks/lc-contact-map.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'lc_brand_grid',
				'title'           => 'LC Brand Grid',
				'render_template' => 'blocks/lc-brand-grid.php',
				'category'        => 'layout',
				'icon'            => 'excerpt-view',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);
	}
}
add_action( 'acf/init', 'acf_blocks' );

// Auto-sync ACF field groups from acf-json folder.
add_filter(
	'acf/settings/save_json',
	function ( $path ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		return get_stylesheet_directory() . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		unset( $paths[0] );
		$paths[] = get_stylesheet_directory() . '/acf-json';
		return $paths;
	}
);

/**
 * Modifies the arguments for core block types to add a custom render callback.
 *
 * @param  array  $args The block type arguments.
 * @param  string $name The block type name.
 * @return array Modified block type arguments.
 */
function core_image_block_type_args( $args, $name ) {
	if ( 'core/paragraph' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/heading' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/list' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	return $args;
}
add_filter( 'register_block_type_args', 'core_image_block_type_args', 10, 3 );

/**
 * Helper function to detect if footer.php is being rendered.
 *
 * @return bool True if footer.php is being rendered, false otherwise.
 */
function is_footer_rendering() {
	$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
	foreach ( $backtrace as $trace ) {
		if ( isset( $trace['file'] ) && basename( $trace['file'] ) === 'footer.php' ) {
			return true;
		}
	}
	return false;
}

/**
 * Adds a container div around the block content unless footer.php is being rendered.
 *
 * @param array  $attributes The block attributes.
 * @param string $content   The block content.
 * @return string The modified block content wrapped in a container div.
 */
function modify_core_add_container( $attributes, $content ) {
	if ( is_footer_rendering() ) {
		return $content;
	}

	ob_start();
	?>
	<div class="container">
		<?= wp_kses_post( $content ); ?>
	</div>
	<?php
	$content = ob_get_clean();
	return $content;
}

// Note: Avoid manipulating wpautop filters globally. Handle formatting in templates
// by rendering only non-empty strings and avoiding stray whitespace.
