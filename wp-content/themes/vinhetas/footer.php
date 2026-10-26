<?php
/**
 * Site footer.
 *
 * @package vinhetas
 */

?>
<footer class="v-footer">
	<div class="v-footer__inner">
		<div class="v-footer__brand">
			<strong>Vinhetas Algarve S.A.</strong>
			<span><?php esc_html_e( 'Poço Barreto · Silves · Algarve, Portugal', 'vinhetas' ); ?></span>
			<a href="mailto:<?php echo esc_attr( antispambot( 'ola@vinhetas.pt' ) ); ?>"><?php echo esc_html( antispambot( 'ola@vinhetas.pt' ) ); ?></a>
		</div>
		<p class="v-footer__legal">
			<?php esc_html_e( 'This website is for information only. It is not an offer of securities or an invitation to invest. Plans, programme and images are illustrative and subject to planning approval and operator input.', 'vinhetas' ); ?>
		</p>
		<p class="v-footer__copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Vinhetas Algarve S.A.</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
