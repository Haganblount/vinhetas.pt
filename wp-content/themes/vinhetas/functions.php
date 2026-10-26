<?php
/**
 * Vinhetas Algarve theme setup.
 *
 * @package vinhetas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VINHETAS_VERSION', '1.0.0' );

require get_template_directory() . '/inc/customizer.php';

/**
 * Theme supports.
 */
function vinhetas_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

	add_image_size( 'vinhetas-hero', 2400, 1350, true );
	add_image_size( 'vinhetas-wide', 1800, 1013, true );
	add_image_size( 'vinhetas-square', 900, 900, true );
}
add_action( 'after_setup_theme', 'vinhetas_setup' );

/**
 * Styles. Fonts use the system Helvetica stack (no font files are shipped).
 */
function vinhetas_assets() {
	$css = get_template_directory() . '/assets/css/site.css';
	// Rounded face for numbers where Arial Rounded / Helvetica Rounded isn't installed.
	wp_enqueue_style(
		'vinhetas-rounded',
		'https://fonts.googleapis.com/css2?family=Nunito:wght@800&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'vinhetas',
		get_template_directory_uri() . '/assets/css/site.css',
		array( 'vinhetas-rounded' ),
		file_exists( $css ) ? (string) filemtime( $css ) : VINHETAS_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'vinhetas_assets' );

/**
 * Meta description for the front page.
 */
function vinhetas_meta_description() {
	if ( is_front_page() ) {
		echo '<meta name="description" content="' . esc_attr__( 'Vinhetas Algarve is planning a 100% private care campus near Silves, Portugal: memory care, assisted living and a small hospice and rehabilitation wing. We are looking for operating partners.', 'vinhetas' ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'vinhetas_meta_description', 1 );

/**
 * Prints a Customizer image, or a labelled placeholder when the slot is empty.
 *
 * @param string $setting Theme mod key.
 * @param string $size    Registered image size.
 * @param string $label   Placeholder label.
 * @param string $ratio   CSS aspect ratio, e.g. "16 / 9".
 * @param string $note    Size hint shown in the placeholder.
 */
function vinhetas_media( $setting, $size, $label, $ratio, $note ) {
	$id  = absint( get_theme_mod( $setting ) );
	$alt = get_theme_mod( $setting . '_alt', '' );

	if ( $id && wp_attachment_is_image( $id ) ) {
		echo wp_get_attachment_image(
			$id,
			$size,
			false,
			array(
				'class'   => 'v-media__img',
				'alt'     => $alt ? $alt : get_post_meta( $id, '_wp_attachment_image_alt', true ),
				'loading' => 'vinhetas-hero' === $size ? 'eager' : 'lazy',
				'style'   => 'aspect-ratio:' . esc_attr( $ratio ),
			)
		);
		return;
	}

	printf(
		'<div class="v-placeholder" style="aspect-ratio:%1$s"><span class="v-placeholder__label">%2$s</span><span class="v-placeholder__note">%3$s</span></div>',
		esc_attr( $ratio ),
		esc_html( $label ),
		esc_html( $note )
	);
}

/**
 * The enquiry form from the Vinhetas Enquiries plugin, or a mailto fallback.
 */
function vinhetas_enquiry_form() {
	if ( shortcode_exists( 'vinhetas_enquiry' ) ) {
		echo do_shortcode( '[vinhetas_enquiry]' );
		return;
	}
	printf(
		'<p class="v-contact__fallback"><a class="v-button" href="mailto:%1$s">%1$s</a></p>',
		esc_attr( antispambot( 'ola@vinhetas.pt' ) )
	);
}
