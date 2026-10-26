<?php
/**
 * Site header.
 *
 * @package vinhetas
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="v-skip" href="#main"><?php esc_html_e( 'Skip to content', 'vinhetas' ); ?></a>
<header class="v-header">
	<div class="v-header__inner">
		<a class="v-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-wordmark.png' ); ?>" width="260" height="56" alt="<?php esc_attr_e( 'Vinhetas Algarve', 'vinhetas' ); ?>">
		</a>
		<nav class="v-nav" aria-label="<?php esc_attr_e( 'Main', 'vinhetas' ); ?>">
			<?php $home = is_front_page() ? '' : home_url( '/' ); ?>
			<a href="<?php echo esc_url( $home . '#campus' ); ?>"><?php esc_html_e( 'Campus', 'vinhetas' ); ?></a>
			<a href="<?php echo esc_url( $home . '#site' ); ?>"><?php esc_html_e( 'Site', 'vinhetas' ); ?></a>
			<a href="<?php echo esc_url( $home . '#planning' ); ?>"><?php esc_html_e( 'Planning', 'vinhetas' ); ?></a>
			<a class="v-nav__cta" href="<?php echo esc_url( $home . '#contact' ); ?>"><?php esc_html_e( 'Partner with us', 'vinhetas' ); ?></a>
		</nav>
	</div>
</header>
