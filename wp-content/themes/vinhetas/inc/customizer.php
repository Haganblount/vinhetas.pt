<?php
/**
 * Customizer: "Vinhetas media" image slots.
 *
 * @package vinhetas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image slots used on the front page: key => [label, recommended size].
 *
 * @return array
 */
function vinhetas_media_slots() {
	return array(
		'vinhetas_hero'      => array( __( 'Hero photo: view from the land', 'vinhetas' ), __( 'Landscape, 16:9, at least 2400 × 1350 px', 'vinhetas' ) ),
		'vinhetas_site_1'    => array( __( 'Site photo 1', 'vinhetas' ), __( 'Square, at least 900 × 900 px', 'vinhetas' ) ),
		'vinhetas_site_2'    => array( __( 'Site photo 2', 'vinhetas' ), __( 'Square, at least 900 × 900 px', 'vinhetas' ) ),
		'vinhetas_site_3'    => array( __( 'Site photo 3', 'vinhetas' ), __( 'Square, at least 900 × 900 px', 'vinhetas' ) ),
		'vinhetas_rendering' => array( __( 'Campus concept rendering', 'vinhetas' ), __( 'Landscape, 16:9, at least 1800 × 1013 px', 'vinhetas' ) ),
	);
}

/**
 * Register the section, one image control and one alt-text field per slot.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function vinhetas_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'vinhetas_media',
		array(
			'title'       => __( 'Vinhetas media', 'vinhetas' ),
			'description' => __( 'Photos and renderings for the home page. Empty slots show a grey placeholder with the size they need.', 'vinhetas' ),
			'priority'    => 30,
		)
	);

	foreach ( vinhetas_media_slots() as $key => $slot ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$key,
				array(
					'label'       => $slot[0],
					'description' => $slot[1],
					'section'     => 'vinhetas_media',
					'mime_type'   => 'image',
				)
			)
		);

		$wp_customize->add_setting(
			$key . '_alt',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$key . '_alt',
			array(
				/* translators: %s: image slot name */
				'label'   => sprintf( __( '%s: description for screen readers', 'vinhetas' ), $slot[0] ),
				'section' => 'vinhetas_media',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'vinhetas_customize_register' );
