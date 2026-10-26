<?php
/**
 * Fallback template for pages, posts and archives.
 *
 * @package vinhetas
 */

get_header();
?>
<main id="main" class="v-main v-page">
	<div class="v-wrap v-prose">
		<?php if ( have_posts() ) : ?>
			<?php if ( ! is_singular() ) : ?>
				<h1 class="v-h2"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
			<?php endif; ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'v-article' ); ?>>
					<?php if ( is_singular() ) : ?>
						<h1 class="v-h2"><?php the_title(); ?></h1>
						<?php the_content(); ?>
					<?php else : ?>
						<h2 class="v-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<h1 class="v-h2"><?php esc_html_e( 'Nothing here', 'vinhetas' ); ?></h1>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'vinhetas' ); ?></a></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
