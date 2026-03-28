<?php
/**
 * Utility functions for the theme.
 *
 * This file contains various utility functions used throughout the theme,
 * including phone parsing, shortcodes, social media integrations, and more.
 *
 * @package lc-eternal2025
 * @since 1.0.0
 */

/**
 * Parse a phone number into a tel: link format.
 *
 * @param string $phone The phone number to parse.
 * @return string The parsed phone number.
 */
function parse_phone( $phone ) {
	$phone = preg_replace( '/\s+/', '', $phone );
	$phone = preg_replace( '/\(0\)/', '', $phone );
	$phone = preg_replace( '/[\(\)\.]/', '', $phone );
	$phone = preg_replace( '/-/', '', $phone );
	$phone = preg_replace( '/^0/', '+44', $phone );
	return $phone;
}

/**
 * Add extra spacing between line breaks in content.
 *
 * @param string $content The content to process.
 * @return string The content with extra spacing between line breaks.
 */
function split_lines( $content ) {
	$content = preg_replace( '/<br \/>/', '<br>&nbsp;<br>', $content );
	return $content;
}

/**
 * Load, augment, and sanitize an SVG from attachment ID or local file path.
 *
 * @param int|string   $svg_source Attachment ID or local SVG file path/URL.
 * @param string|array $classes    Optional CSS class or classes to add to the root SVG element.
 * @param string|int   $width      Optional width attribute for the root SVG element.
 * @param string|int   $height     Optional height attribute for the root SVG element.
 * @return string Sanitized SVG markup, or an empty string if unavailable.
 */
function lc_sanitise_svg( $svg_source, $classes = '', $width = '', $height = '' ) {
	$is_dimension = static function ( $value ) {
		if ( ! is_scalar( $value ) ) {
			return false;
		}

		$value = trim( (string) $value );

		if ( '' === $value ) {
			return false;
		}

		return 1 === preg_match( '/^\d+(?:\.\d+)?(?:px|em|rem|%|vh|vw|vmin|vmax|pt|pc|cm|mm|in)?$/i', $value );
	};

	$sanitize_dimension = static function ( $value ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( 1 === preg_match( '/^\d+(?:\.\d+)?(?:px|em|rem|%|vh|vw|vmin|vmax|pt|pc|cm|mm|in)?$/i', $value ) ) {
			return $value;
		}

		return '';
	};

	if ( ! is_array( $classes ) && $is_dimension( $classes ) ) {
		if ( '' === $width && '' === $height ) {
			$width   = $classes;
			$classes = '';
		} elseif ( '' === $height && $is_dimension( $width ) ) {
			$height  = $width;
			$width   = $classes;
			$classes = '';
		}
	}

	$width  = $sanitize_dimension( $width );
	$height = $sanitize_dimension( $height );

	if ( empty( $svg_source ) ) {
		return '';
	}

	$file_path = '';

	if ( is_numeric( $svg_source ) ) {
		$attachment_id = (int) $svg_source;

		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return '';
		}

		$file_path = get_attached_file( $attachment_id );
	} elseif ( is_string( $svg_source ) ) {
		$file_path = trim( $svg_source );

		$theme_uri = get_stylesheet_directory_uri();
		if ( 0 === strpos( $file_path, $theme_uri ) ) {
			$file_path = get_stylesheet_directory() . substr( $file_path, strlen( $theme_uri ) );
		}

		$parent_theme_uri = get_template_directory_uri();
		if ( 0 === strpos( $file_path, $parent_theme_uri ) ) {
			$file_path = get_template_directory() . substr( $file_path, strlen( $parent_theme_uri ) );
		}

		if ( ! preg_match( '#^([a-zA-Z]:[\\/]|/)#', $file_path ) ) {
			$theme_relative = ltrim( $file_path, '/\\' );
			$child_path     = trailingslashit( get_stylesheet_directory() ) . $theme_relative;
			$parent_path    = trailingslashit( get_template_directory() ) . $theme_relative;

			if ( file_exists( $child_path ) ) {
				$file_path = $child_path;
			} elseif ( file_exists( $parent_path ) ) {
				$file_path = $parent_path;
			}
		}

		if ( 'svg' !== strtolower( (string) pathinfo( $file_path, PATHINFO_EXTENSION ) ) ) {
			return '';
		}
	} else {
		return '';
	}

	if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
		return '';
	}

	$svg_markup = file_get_contents( $file_path );

	if ( false === $svg_markup || '' === trim( $svg_markup ) ) {
		return '';
	}

	$classes = is_array( $classes ) ? $classes : preg_split( '/\s+/', trim( (string) $classes ) );
	$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

	$svg_markup = preg_replace( '/<\?xml.*?\?>/i', '', $svg_markup );
	$svg_markup = preg_replace_callback(
		'/<svg\b([^>]*)>/i',
		static function ( $matches ) use ( $classes, $width, $height ) {
			$attributes = $matches[1];

			if ( preg_match( '/\bclass=("|\')(.*?)(\1)/i', $attributes, $class_matches ) ) {
				$existing_classes = preg_split( '/\s+/', trim( $class_matches[2] ) );
				$all_classes      = array_unique( array_filter( array_merge( $existing_classes, $classes ) ) );
				$attributes       = preg_replace( '/\bclass=("|\')(.*?)(\1)/i', 'class="' . esc_attr( implode( ' ', $all_classes ) ) . '"', $attributes, 1 );
			} elseif ( ! empty( $classes ) ) {
				$attributes .= ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
			}

			if ( ! preg_match( '/\baria-hidden=/i', $attributes ) ) {
				$attributes .= ' aria-hidden="true"';
			}

			if ( ! preg_match( '/\bfocusable=/i', $attributes ) ) {
				$attributes .= ' focusable="false"';
			}

			if ( '' !== $width ) {
				if ( preg_match( '/\bwidth=("|\')(.*?)(\1)/i', $attributes ) ) {
					$attributes = preg_replace( '/\bwidth=("|\')(.*?)(\1)/i', 'width="' . esc_attr( $width ) . '"', $attributes, 1 );
				} else {
					$attributes .= ' width="' . esc_attr( $width ) . '"';
				}
			}

			if ( '' !== $height ) {
				if ( preg_match( '/\bheight=("|\')(.*?)(\1)/i', $attributes ) ) {
					$attributes = preg_replace( '/\bheight=("|\')(.*?)(\1)/i', 'height="' . esc_attr( $height ) . '"', $attributes, 1 );
				} else {
					$attributes .= ' height="' . esc_attr( $height ) . '"';
				}
			}

			return '<svg' . $attributes . '>';
		},
		$svg_markup,
		1
	);

	$allowed_svg_tags = array(
		'svg'            => array(
			'aria-hidden'         => true,
			'class'               => true,
			'data-name'           => true,
			'fill'                => true,
			'focusable'           => true,
			'height'              => true,
			'id'                  => true,
			'preserveaspectratio' => true,
			'role'                => true,
			'stroke'              => true,
			'stroke-width'        => true,
			'viewbox'             => true,
			'width'               => true,
			'xmlns'               => true,
			'xmlns:xlink'         => true,
		),
		'g'              => array(
			'class'             => true,
			'clip-path'         => true,
			'data-name'         => true,
			'fill'              => true,
			'fill-rule'         => true,
			'id'                => true,
			'mask'              => true,
			'opacity'           => true,
			'stroke'            => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'stroke-width'      => true,
			'transform'         => true,
		),
		'path'           => array(
			'class'             => true,
			'clip-rule'         => true,
			'd'                 => true,
			'fill'              => true,
			'fill-rule'         => true,
			'opacity'           => true,
			'stroke'            => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'stroke-width'      => true,
			'transform'         => true,
		),
		'circle'         => array(
			'class'        => true,
			'cx'           => true,
			'cy'           => true,
			'fill'         => true,
			'opacity'      => true,
			'r'            => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'ellipse'        => array(
			'class'        => true,
			'cx'           => true,
			'cy'           => true,
			'fill'         => true,
			'opacity'      => true,
			'rx'           => true,
			'ry'           => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'line'           => array(
			'class'          => true,
			'stroke'         => true,
			'stroke-linecap' => true,
			'stroke-width'   => true,
			'x1'             => true,
			'x2'             => true,
			'y1'             => true,
			'y2'             => true,
		),
		'polygon'        => array(
			'class'        => true,
			'fill'         => true,
			'opacity'      => true,
			'points'       => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'polyline'       => array(
			'class'        => true,
			'fill'         => true,
			'opacity'      => true,
			'points'       => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'rect'           => array(
			'class'        => true,
			'fill'         => true,
			'height'       => true,
			'opacity'      => true,
			'rx'           => true,
			'ry'           => true,
			'stroke'       => true,
			'stroke-width' => true,
			'width'        => true,
			'x'            => true,
			'y'            => true,
		),
		'defs'           => array(),
		'style'          => array(
			'type' => true,
		),
		'clippath'       => array(
			'id' => true,
		),
		'mask'           => array(
			'id'        => true,
			'maskunits' => true,
			'x'         => true,
			'y'         => true,
			'width'     => true,
			'height'    => true,
		),
		'lineargradient' => array(
			'id' => true,
			'x1' => true,
			'x2' => true,
			'y1' => true,
			'y2' => true,
		),
		'radialgradient' => array(
			'cx' => true,
			'cy' => true,
			'fx' => true,
			'fy' => true,
			'id' => true,
			'r'  => true,
		),
		'stop'           => array(
			'offset'       => true,
			'stop-color'   => true,
			'stop-opacity' => true,
		),
		'title'          => array(),
		'desc'           => array(),
		'use'            => array(
			'href'       => true,
			'width'      => true,
			'height'     => true,
			'x'          => true,
			'xlink:href' => true,
			'y'          => true,
		),
	);

	return wp_kses( $svg_markup, $allowed_svg_tags );
}

add_shortcode(
	'contact_address',
	function () {
		$output = get_field( 'contact_address', 'options' );
		return $output;
	}
);

/**
 * Generate contact phone shortcode with optional icon and custom text.
 *
 * @param array $atts {
 *     Optional. Shortcode attributes.
 *
 *     @type string $class CSS class for the anchor element. Default empty.
 *     @type string $text  Custom text to display. Default phone number.
 *     @type bool   $icon  Whether to show phone icon. Default false.
 * }
 * @return string|null HTML anchor tag with phone link or null if no phone is set.
 */
function contact_phone( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'class' => '',
			'text'  => '',
			'icon'  => false,
		),
		$atts,
		'contact_phone'
	);

	if ( get_field( 'contact_phone', 'options' ) ) {
		$phone       = get_field( 'contact_phone', 'options' );
		$icon_html   = ( 'true' === $atts['icon'] || true === $atts['icon'] ) ? '<i class="fa-solid fa-phone"></i> ' : '';
		$anchor_text = $icon_html . ( ! empty( $atts['text'] ) ? wp_kses_post( $atts['text'] ) : esc_html( $phone ) );
		$class       = esc_attr( $atts['class'] );

		return '<a href="tel:' . parse_phone( $phone ) . '" class="' . $class . '">' . $anchor_text . '</a>';
	}
}
add_shortcode( 'contact_phone', 'contact_phone' );

/**
 * Generate contact email shortcode with optional icon and custom text.
 *
 * @param array $atts {
 *     Optional. Shortcode attributes.
 *
 *     @type string $class CSS class for the anchor element. Default empty.
 *     @type string $text  Custom text to display. Default email address.
 *     @type bool   $icon  Whether to show email icon. Default false.
 * }
 * @return string|null HTML anchor tag with email link or null if no email is set.
 */
function contact_email( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'class' => '',
			'text'  => '',
			'icon'  => false,
		),
		$atts,
		'contact_email'
	);

	if ( get_field( 'contact_email', 'options' ) ) {
		$email            = get_field( 'contact_email', 'options' );
		$icon_html        = ( 'true' === $atts['icon'] || true === $atts['icon'] ) ? '<i class="fa-solid fa-envelope"></i> ' : '';
		$anchor_text      = $icon_html . ( ! empty( $atts['text'] ) ? wp_kses_post( $atts['text'] ) : esc_html( antispambot( $email ) ) );
		$class            = esc_attr( $atts['class'] );
		$obfuscated_email = antispambot( $email );

		return '<a href="mailto:' . esc_attr( $obfuscated_email ) . '" class="' . $class . '">' . $anchor_text . '</a>';
	}
}
add_shortcode( 'contact_email', 'contact_email' );


/**
 * Generates a social icon shortcode based on the provided type.
 *
 * @param array $atts Attributes passed to the shortcode.
 * @return string The HTML for the social icon or an empty string if the type is invalid.
 */
function social_icon_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'type' => '' ), $atts );
	if ( ! $atts['type'] ) {
		return '';
	}

	$social = get_field( 'socials', 'options' );
	$urls   = array(
		'facebook'    => $social['facebook_url'] ?? '',
		'instagram'   => $social['instagram_url'] ?? '',
		'x-twitter'   => $social['twitter_url'] ?? '',
		'pinterest'   => $social['pinterest_url'] ?? '',
		'youtube'     => $social['youtube_url'] ?? '',
		'linkedin-in' => $social['linkedin_url'] ?? '',
	);

	if ( ! isset( $urls[ $atts['type'] ] ) || empty( $urls[ $atts['type'] ] ) ) {
		return '';
	}

	$url  = esc_url( $urls[ $atts['type'] ] );
	$icon = esc_attr( $atts['type'] );

	// Create readable label for accessibility.
	$labels = array(
		'facebook'    => 'Facebook',
		'instagram'   => 'Instagram',
		'x-twitter'   => 'X (Twitter)',
		'pinterest'   => 'Pinterest',
		'youtube'     => 'YouTube',
		'linkedin-in' => 'LinkedIn',
	);
	$label  = $labels[ $atts['type'] ] ?? ucfirst( $atts['type'] );

	return '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer" aria-label="' . esc_attr( $label ) . '"><i class="fa-brands fa-' . $icon . '" aria-hidden="true"></i></a>';
}

// Register individual social icon shortcodes.
$social_types = array( 'facebook', 'instagram', 'twitter', 'pinterest', 'youtube', 'linkedin' );
foreach ( $social_types as $social_type ) {
	add_shortcode(
		'social_' . $social_type . '_icon',
		function () use ( $social_type ) {
			return social_icon_shortcode( array( 'type' => $social_type ) );
		}
	);
}

// Generate a single shortcode to output all social icons.
add_shortcode(
	'social_icons',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts,
			'social_icons'
		);

		$social = get_field( 'socials', 'options' );
		if ( ! $social ) {
			return '';
		}

		$icons      = array();
		$social_map = array(
			'twitter'   => 'x-twitter',
			'facebook'  => 'facebook-f',
			'instagram' => 'instagram',
			'pinterest' => 'pinterest',
			'youtube'   => 'youtube',
			'linkedin'  => 'linkedin-in',
		);

		$labels = array(
			'twitter'   => 'X (Twitter)',
			'facebook'  => 'Facebook',
			'instagram' => 'Instagram',
			'pinterest' => 'Pinterest',
			'youtube'   => 'YouTube',
			'linkedin'  => 'LinkedIn',
		);

		foreach ( $social_map as $key => $icon ) {
			if ( ! empty( $social[ $key . '_url' ] ) ) {
				$url     = esc_url( $social[ $key . '_url' ] );
				$label   = esc_attr( $labels[ $key ] );
				$icons[] = '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer" aria-label="' . $label . '"><i class="fa-brands fa-' . $icon . '" aria-hidden="true"></i></a>';
			}
		}

		$class = esc_attr( trim( $atts['class'] ) );

		return ! empty( $icons ) ? '<div class="social-icons ' . $class . '">' . implode( ' ', $icons ) . '</div>' : '';
	}
);

/**
 * Grab the specified data like Thumbnail URL of a publicly embeddable video hosted on Vimeo.
 *
 * @param  str $video_id The ID of a Vimeo video.
 * @param  str $data Video data to be fetched.
 * @return str            The specified data
 */
function get_vimeo_data_from_id( $video_id, $data ) {
	// width can be 100, 200, 295, 640, 960 or 1280.
	$request = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/' . $video_id . '&width=960' );

	$response = wp_remote_retrieve_body( $request );

	$video_array = json_decode( $response, true );

	return $video_array[ $data ];
}

/**
 * Add custom styles to the Gutenberg editor admin area.
 *
 * Sets maximum widths for blocks, wide blocks, and full-width blocks
 * in the WordPress block editor.
 *
 * @return void
 */
function lc_gutenberg_admin_styles() {
	echo '
        <style>
            /* Main column width */
            .wp-block {
                max-width: 1040px;
            }
 
            /* Width of "wide" blocks */
            .wp-block[data-align="wide"] {
                max-width: 1080px;
            }
 
            /* Width of "full-wide" blocks */
            .wp-block[data-align="full"] {
                max-width: none;
            }	
        </style>
    ';
}
add_action( 'admin_head', 'lc_gutenberg_admin_styles' );


if ( is_admin() ) {
	/**
	 * Disable the fullscreen editor mode by default in the WordPress block editor.
	 *
	 * This function adds inline JavaScript to toggle off the fullscreen mode
	 * if it is currently active when the editor loads.
	 *
	 * @return void
	 */
	// phpcs:disable
	function lc_disable_editor_fullscreen_by_default() {
		$script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";

		// ACF known bug workaround: prevent switchEditors.go from forcing focus when enabling TinyMCE.
		// See: https://support.advancedcustomfields.com/forums/topic/bug-focus-forced-down-page-when-inserting-removing-blocks/
		$script .= "\n(function(){ if (!window.wp || !wp.data) { return; } wp.domReady(function(){
			function isTypingInBlockEditor(){ try { var sel = wp.data.select('core/block-editor'); return !!(sel && (sel.getSelectionStart() || sel.getSelectedBlock())); } catch(e){ return false; } }

			try {
				if (window.switchEditors && typeof window.switchEditors.go === 'function') {
					var originalGo = window.switchEditors.go;
					window.switchEditors.go = function(id, mode){
						// If we're in block editor context, avoid forced focus jumps.
						if (isTypingInBlockEditor()) {
							// If the target element already has an editor, skip focus forcing.
							var el = document.getElementById(id);
							var alreadyInit = false;
							if (window.tinymce) {
								var ed = window.tinymce.get(id);
								alreadyInit = !!ed;
							}
							if (alreadyInit) {
								// Do not change focus; return without invoking originalGo which can refocus.
								return;
							}
						}
						return originalGo.apply(this, arguments);
					};
				}
			} catch(e){}
		}); });";
		wp_add_inline_script( 'wp-blocks', $script );
	}
	add_action( 'enqueue_block_editor_assets', 'lc_disable_editor_fullscreen_by_default' );
	// phpcs:enable
}

/**
 * Get the top-level ancestor ID for the current post.
 *
 * @return int The ID of the top-level ancestor post, or the current post ID if it has no parent.
 */
function get_the_top_ancestor_id() {
	global $post;
	if ( $post->post_parent ) {
		$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
		return $ancestors[0];
	} else {
		return $post->ID;
	}
}

/**
 * Convert time string to ISO 8601 duration format.
 *
 * @param string $str The time string in HH:MM:SS format.
 * @return string The ISO 8601 duration format (e.g., PT1H30M45S).
 */
function lc_time_to_8601( $str ) {
	$time   = explode( ':', $str );
	$output = 'PT' . $time[0] . 'H' . $time[1] . 'M' . $time[2] . 'S';
	return $output;
}

/**
 * Generate a random string of specified length.
 *
 * @param int    $length   The length of the random string to generate. Default 64.
 * @param string $keyspace The characters to use for generating the string. Default alphanumeric.
 * @return string The generated random string.
 * @throws \RangeException If length is less than 1.
 */
function random_str(
	int $length = 64,
	string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
	if ( $length < 1 ) {
		throw new \RangeException( 'Length must be a positive integer' );
	}
	$pieces = array();
	$max    = mb_strlen( $keyspace, '8bit' ) - 1;
	for ( $i = 0; $i < $length; ++$i ) {
		$pieces[] = $keyspace[ random_int( 0, $max ) ];
	}
	return implode( '', $pieces );
}

/**
 * Generate social share buttons for a post.
 *
 * @param int $id The post ID to share.
 * @return string HTML markup for social share buttons.
 */
function lc_social_share( $id ) {
	ob_start();
	$url = get_the_permalink( $id );

	?>
<div class="text-larger text--yellow mb-5">
	<div class="h4 text-dark">Share</div>
	<a target='_blank' href='https://twitter.com/share?url=<?php echo esc_attr( $url ); ?>'
		class="mr-2"><i class='fab fa-twitter'></i></a>
	<a target='_blank'
		href='http://www.linkedin.com/shareArticle?url=<?php echo esc_attr( $url ); ?>'
		class="mr-2"><i class='fab fa-linkedin-in'></i></a>
	<a target='_blank'
		href='http://www.facebook.com/sharer.php?u=<?php echo esc_attr( $url ); ?>'><i
			class='fab fa-facebook-f'></i></a>
</div>
	<?php

	$out = ob_get_clean();
	return $out;
}

/**
 * Enable HTTP Strict Transport Security (HSTS) header.
 *
 * Adds the HSTS header to enforce HTTPS connections for one year.
 *
 * @return void
 */
function enable_strict_transport_security_hsts_header() {
	header( 'Strict-Transport-Security: max-age=31536000' );
}
add_action( 'send_headers', 'enable_strict_transport_security_hsts_header' );

/**
 * Convert field content to an HTML list.
 *
 * @param string $field The field content to convert.
 * @return string The HTML list items.
 */
function lc_list( $field ) {
	ob_start();
	$field   = strip_tags( $field, '<br />' );
	$bullets = preg_split( "/\r\n|\n|\r/", $field );
	foreach ( $bullets as $b ) {
		if ( '' === $b ) {
			continue;
		}
		?>
<li><?php echo esc_html( $b ); ?></li>
		<?php
	}
	return ob_get_clean();
}

/**
 * Returns img tag with srcset.
 *
 * @param string $id The post ID.
 * @return string
 */
function lc_featured_image( $id ) {
	$tag = get_the_post_thumbnail(
		$id,
		'full',
		array(
			'srcset' => wp_get_attachment_image_url( get_post_thumbnail_id(), 'medium' ) . ' 480w, ' .
				wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' ) . ' 640w, ' .
				wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) . ' 960w',
		)
	);
	return $tag;
}

/**
 * Remove deprecated pre-publish checks toggle to avoid console errors in WP 6.8+.
 * Kept intentionally empty; the previous API `disablePrePublishChecks()` no longer exists.
 */
// (No-op)

add_action(
	'admin_init',
	function () {
		// Redirect any user trying to access comments page.
		global $pagenow;

		if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

		// Remove comments metabox from dashboard.
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

		// Disable support for comments and trackbacks in post types.
		foreach ( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}
);

// Close comments on the front-end.
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments.
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Remove comments page in menu.
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);

// Remove comments links from admin bar.
add_action(
	'init',
	function () {
		if ( is_admin_bar_showing() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}
	}
);

/**
 * Remove comments menu from the admin bar.
 *
 * @return void
 */
function remove_comments() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'remove_comments' );

/**
 * Estimate reading time in minutes for given content.
 *
 * @param string $content          The content to analyze.
 * @param int    $words_per_minute Average reading speed. Default 300.
 * @param bool   $with_gutenberg   Whether to parse Gutenberg blocks. Default false.
 * @param bool   $formatted        Whether to return formatted HTML. Default false.
 * @return int|string Reading time in minutes, or formatted HTML if $formatted is true.
 */
function estimate_reading_time_in_minutes( $content = '', $words_per_minute = 300, $with_gutenberg = false, $formatted = false ) {
	// In case if content is build with gutenberg parse blocks.
	if ( $with_gutenberg ) {
		$blocks       = parse_blocks( $content );
		$content_html = '';

		foreach ( $blocks as $block ) {
			$content_html .= render_block( $block );
		}

		$content = $content_html;
	}

	// Remove HTML tags from string.
	$content = wp_strip_all_tags( $content );

	// When content is empty return 0.
	if ( ! $content ) {
		return 0;
	}

	// Count words containing string.
	$words_count = str_word_count( $content );

	// Calculate time for read all words and round.
	$minutes = ceil( $words_count / $words_per_minute );

	if ( $formatted ) {
		$minutes = '<p class="reading">Estimated reading time ' . $minutes . ' ' . pluralise( $minutes, 'minute' ) . '</p>';
	}

	return $minutes;
}

/**
 * Pluralise a word based on quantity.
 *
 * @param int         $quantity The quantity to check.
 * @param string      $singular The singular form.
 * @param string|null $plural The plural form (optional).
 * @return string The pluralized word.
 */
function pluralise( $quantity, $singular, $plural = null ) {
	if ( 1 === $quantity || ! strlen( $singular ) ) {
		return $singular;
	}
	if ( null !== $plural ) {
		return $plural;
	}

	$last_letter = strtolower( $singular[ strlen( $singular ) - 1 ] );
	switch ( $last_letter ) {
		case 'y':
			return substr( $singular, 0, -1 ) . 'ies';
		case 's':
			return $singular . 'es';
		default:
			return $singular . 's';
	}
}

/**
 * Converts a file size in bytes to a human-readable format.
 *
 * @param int $size      The size in bytes.
 * @param int $precision The number of decimal places to round to. Default is 2.
 * @return string        The formatted size with appropriate unit (e.g., KB, MB).
 */
function format_bytes( $size, $precision = 2 ) {
	if ( $size <= 0 ) {
		return '0 B'; // Return 0 bytes as a default value.
	}

	$base     = log( $size, 1024 );
	$suffixes = array( '', 'K', 'M', 'G', 'T' );

	return round( pow( 1024, $base - floor( $base ) ), $precision ) . ' ' . $suffixes[ floor( $base ) ];
}

/**
 * Slugify text for URL-safe strings.
 *
 * @param string $text The text to slugify.
 * @param string $divider The divider character to use.
 * @return string The slugified text.
 */
function cbslugify( $text, string $divider = '-' ) {
	// Replace non letter or digits by divider.
	$text = preg_replace( '~[^\pL\d]+~u', $divider, $text );

	// Transliterate.
	$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

	// Remove unwanted characters.
	$text = preg_replace( '~[^-\w]+~', '', $text );

	// Trim.
	$text = trim( $text, $divider );

	// Remove duplicate divider.
	$text = preg_replace( '~-+~', $divider, $text );

	// Lowercase.
	$text = strtolower( $text );

	if ( empty( $text ) ) {
		return 'n-a';
	}

	return $text;
}

/**
 * Convert field content to an HTML list.
 *
 * @param string $field The field content to convert.
 * @return string The HTML list.
 */
function cb_list( $field ) {
	ob_start();
	$field   = strip_tags( $field, '<br />' );
	$bullets = preg_split( "/\r\n|\n|\r/", $field );
	foreach ( $bullets as $b ) {
		if ( '' === $b ) {
			continue;
		}
		?>
		<li><?php echo esc_html( $b ); ?></li>
		<?php
	}
	return ob_get_clean();
}

/**
 * Convert a regular list to FontAwesome list format
 *
 * @param string $content The HTML content containing the list.
 * @param string $icon_class The FontAwesome icon class to use (default: fa-check).
 * @return string The converted HTML with FontAwesome list structure.
 */
function convert_to_fa_list( $content, $icon_class = 'fa-check' ) {
	// Replace ul class with fa-ul and remove the icon class from ul.
	$content = preg_replace( '/class="wp-block-list([^"]*)"/', 'class="fa-ul$1"', $content );

	// Remove fa-list trigger class from the ul class attribute.
	$content = preg_replace( '/class="fa-ul([^"]*)\s+fa-list([^"]*)"/', 'class="fa-ul$1$2"', $content );

	// Remove the icon class from the ul class attribute (e.g., remove fa-star from fa-ul fa-star).
	$content = preg_replace( '/class="fa-ul([^"]*)\s+' . preg_quote( $icon_class, '/' ) . '([^"]*)"/', 'class="fa-ul$1$2"', $content );

	// Clean up any double spaces in class attribute.
	$content = preg_replace( '/class="([^"]*)\s+([^"]*)"/', 'class="$1 $2"', $content );
	$content = preg_replace( '/class="([^"]*)\s\s+([^"]*)"/', 'class="$1 $2"', $content );

	// Add fa-li icon to each list item.
	$content = preg_replace( '/<li>/', '<li><span class="fa-li"><i class="fa-solid ' . $icon_class . '"></i></span>', $content );

	return $content;
}

/**
 * Disable Contact Form 7 autop (no <p>/<br> wrapping).
 */
add_filter(
	'wpcf7_autop_or_not',
	function () {
		return false;
	}
);

add_action(
	'wpcf7_init',
	function () {
		wpcf7_add_form_tag(
			array( 'honeypot', 'honeypot*' ),
			'lc_cf7_honeypot_form_tag_handler',
			array(
				'name-attr'    => true,
				'do-not-store' => true,
			)
		);
	}
);

/**
 * Handles the honeypot form tag in Contact Form 7.
 *
 * Creates a hidden honeypot field that should remain empty. This helps prevent spam
 * by catching automated form submissions.
 *
 * @param WPCF7_FormTag $tag The form tag object.
 * @return string HTML for the honeypot field.
 */
function lc_cf7_honeypot_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class            = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts                 = array();
	$atts['class']        = $tag->get_class_option( $class );
	$atts['id']           = $tag->get_id_option();
	$atts['message']      = $tag->get_option( 'message', '', true );
	$atts['name']         = $tag->name;
	$atts['type']         = 'text';
	$atts['autocomplete'] = 'off';

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="contact-field" style="position: fixed; top: 0; left: 0; margin: -1px; padding: 0; height: 1px; width: 1px; clip: rect(0 0 0 0); clip-path: inset(50%%); overflow: hidden; white-space: nowrap; border: 0;">
            <label>
                <span>%1$s</span>
                <input %2$s value="" tabindex="-1">
            </label>
            %3$s
        </span>',
		esc_html( ! empty( $atts['message'] ) ? $atts['message'] : __( 'If you are human, leave this field blank.', 'lc-eternal2025' ) ),
		$atts,
		$validation_error
	);

	return $html;
}

add_filter( 'wpcf7_validate_honeypot', 'lc_cf7_honeypot_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_honeypot*', 'lc_cf7_honeypot_validation_filter', 10, 2 );

/**
 * Validates the honeypot field in Contact Form 7.
 *
 * Checks if the honeypot field has been filled out. If it has, the submission
 * is marked as spam.
 *
 * @param WPCF7_Validation $result The validation result object.
 * @param WPCF7_FormTag    $tag    The form tag object.
 * @return WPCF7_Validation The modified validation result.
 */
function lc_cf7_honeypot_validation_filter( $result, $tag ) {
	$name = $tag->name;

	// We don't need nonce verification here as this is handled by CF7.
    // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';

	if ( ! empty( $value ) ) {
		$result->invalidate( $tag, __( 'Spam detected.', 'lc-eternal2025' ) );
	}

	return $result;
}
?>
