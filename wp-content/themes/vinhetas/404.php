<?php
/**
 * Not found.
 *
 * @package vinhetas
 */

get_header();
?>
<main id="main" class="v-main v-page">
	<div class="v-wrap v-prose">
		<p class="v-num">404</p>
		<h1 class="v-h2"><?php esc_html_e( 'Page not found', 'vinhetas' ); ?></h1>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'vinhetas' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
