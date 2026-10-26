<?php
/**
 * Plugin Name: Vinhetas Enquiries
 * Description: Partner enquiry form for vinhetas.pt. Saves enquiries in WordPress (Enquiries menu), emails a notification and exports to CSV. Use the shortcode [vinhetas_enquiry].
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Vinhetas Algarve S.A.
 * License: Proprietary
 * Text Domain: vinhetas-enquiries
 *
 * @package vinhetas-enquiries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VE_POST_TYPE', 've_enquiry' );

/*
 * Settings live in wp-config.php on the server, never in GitHub:
 *
 *   VE_SMTP_USER          the Proton address that sends, e.g. ola@vinhetas.pt
 *   VE_SMTP_TOKEN         a Proton SMTP token for that address
 *   VE_TURNSTILE_SITE_KEY Cloudflare Turnstile site key (Invisible widget)
 *   VE_TURNSTILE_SECRET   Cloudflare Turnstile secret key
 *
 * Each feature switches on only when its settings are present.
 */

/**
 * Whether outgoing mail should go through Proton SMTP.
 *
 * @return bool
 */
function ve_smtp_ready() {
	return defined( 'VE_SMTP_USER' ) && defined( 'VE_SMTP_TOKEN' ) && is_email( VE_SMTP_USER ) && '' !== VE_SMTP_TOKEN;
}

/**
 * Send all site mail through Proton, so it passes vinhetas.pt's SPF, DKIM and DMARC checks.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $mailer Mailer.
 */
function ve_smtp( $mailer ) {
	if ( ! ve_smtp_ready() ) {
		return;
	}
	$mailer->isSMTP();
	$mailer->Host       = defined( 'VE_SMTP_HOST' ) ? VE_SMTP_HOST : 'smtp.protonmail.ch'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->Port       = defined( 'VE_SMTP_PORT' ) ? (int) VE_SMTP_PORT : 587; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->SMTPSecure = 'tls'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->SMTPAuth   = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->Username   = VE_SMTP_USER; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->Password   = VE_SMTP_TOKEN; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->setFrom( VE_SMTP_USER, 'Vinhetas Algarve', false );
	$mailer->Sender = VE_SMTP_USER; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
}
add_action( 'phpmailer_init', 've_smtp' );

add_filter(
	'wp_mail_from',
	function ( $from ) {
		return ve_smtp_ready() ? VE_SMTP_USER : $from;
	}
);
add_filter(
	'wp_mail_from_name',
	function ( $name ) {
		return ve_smtp_ready() ? 'Vinhetas Algarve' : $name;
	}
);

/**
 * Whether Turnstile spam protection is configured.
 *
 * @return bool
 */
function ve_turnstile_ready() {
	return defined( 'VE_TURNSTILE_SITE_KEY' ) && defined( 'VE_TURNSTILE_SECRET' ) && '' !== VE_TURNSTILE_SITE_KEY && '' !== VE_TURNSTILE_SECRET;
}

/**
 * Check a Turnstile token with Cloudflare.
 *
 * @param string $token Token from the form.
 * @return bool
 */
function ve_turnstile_ok( $token ) {
	if ( '' === $token ) {
		return false;
	}
	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => VE_TURNSTILE_SECRET,
				'response' => $token,
			),
		)
	);
	if ( is_wp_error( $response ) ) {
		// Cloudflare unreachable: don't lose a real enquiry; the other checks still apply.
		return true;
	}
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	return ! empty( $body['success'] );
}

/**
 * Invisible Turnstile: nothing shows on the page; the check runs when Send is clicked.
 */
function ve_turnstile_script() {
	if ( ! ve_turnstile_ready() ) {
		return;
	}
	wp_register_script( 've-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_script( 've-turnstile' );
	$site_key = wp_json_encode( VE_TURNSTILE_SITE_KEY );
	$inline   = <<<JS
(function () {
	var form = document.querySelector('.ve-form');
	if (!form) { return; }
	var holder = form.querySelector('.ve-ts');
	var field = form.querySelector('input[name="ve_ts_token"]');
	var widget = null;
	var sending = false;
	function ensure() {
		if (widget !== null || !window.turnstile) { return; }
		widget = window.turnstile.render(holder, {
			sitekey: {$site_key},
			execution: 'execute',
			appearance: 'interaction-only',
			callback: function (token) { field.value = token; sending = true; form.submit(); },
			'error-callback': function () { sending = true; form.submit(); }
		});
	}
	form.addEventListener('submit', function (e) {
		if (sending || field.value) { return; }
		if (!window.turnstile) { return; }
		e.preventDefault();
		ensure();
		window.turnstile.execute(holder);
	});
})();
JS;
	wp_add_inline_script( 've-turnstile', $inline );
}

/**
 * Who the enquiry is from.
 *
 * @return array
 */
function ve_types() {
	return array(
		'operator' => __( 'Care operator', 'vinhetas-enquiries' ),
		'health'   => __( 'Hospital or healthcare group', 'vinhetas-enquiries' ),
		'investor' => __( 'Investor or lender', 'vinhetas-enquiries' ),
		'design'   => __( 'Architect, engineer or consultant', 'vinhetas-enquiries' ),
		'other'    => __( 'Other', 'vinhetas-enquiries' ),
	);
}

/**
 * Private post type that stores enquiries.
 */
function ve_register_post_type() {
	register_post_type(
		VE_POST_TYPE,
		array(
			'labels'          => array(
				'name'          => __( 'Enquiries', 'vinhetas-enquiries' ),
				'singular_name' => __( 'Enquiry', 'vinhetas-enquiries' ),
				'all_items'     => __( 'All enquiries', 'vinhetas-enquiries' ),
				'edit_item'     => __( 'Enquiry', 'vinhetas-enquiries' ),
				'search_items'  => __( 'Search enquiries', 'vinhetas-enquiries' ),
				'not_found'     => __( 'No enquiries yet.', 'vinhetas-enquiries' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 25,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'show_in_rest'    => false,
		)
	);
}
add_action( 'init', 've_register_post_type' );

/**
 * The form.
 *
 * @return string
 */
function ve_shortcode() {
	$status = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'sent' === $status ) {
		return '<div class="ve-note" role="status">' . esc_html__( 'Thank you. Your message has reached us and we will reply within a few working days.', 'vinhetas-enquiries' ) . '</div>';
	}

	$errors = array(
		'missing' => __( 'Please fill in your name, a valid email address and tick the consent box.', 'vinhetas-enquiries' ),
		'expired' => __( 'The form expired. Please send it again.', 'vinhetas-enquiries' ),
		'busy'    => __( 'Too many messages from here in a short time. Please try again later, or email ola@vinhetas.pt.', 'vinhetas-enquiries' ),
	);

	ob_start();
	?>
	<form class="ve-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<?php if ( isset( $errors[ $status ] ) ) : ?>
			<div class="ve-note ve-note--error" role="alert"><?php echo esc_html( $errors[ $status ] ); ?></div>
		<?php endif; ?>
		<input type="hidden" name="action" value="ve_submit">
		<input type="hidden" name="ve_ts" value="<?php echo esc_attr( (string) time() ); ?>">
		<?php
		$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$return  = remove_query_arg( 'enquiry', home_url( strtok( $request, '#' ) ) );
		?>
		<input type="hidden" name="ve_return" value="<?php echo esc_url( $return ); ?>">
		<?php wp_nonce_field( 've_submit', 've_nonce' ); ?>

		<div class="ve-row">
			<div class="ve-field">
				<label for="ve-name"><?php esc_html_e( 'Name', 'vinhetas-enquiries' ); ?> <span class="ve-req" aria-hidden="true">*</span></label>
				<input id="ve-name" name="ve_name" type="text" autocomplete="name" required maxlength="120">
			</div>
			<div class="ve-field">
				<label for="ve-org"><?php esc_html_e( 'Organisation', 'vinhetas-enquiries' ); ?></label>
				<input id="ve-org" name="ve_org" type="text" autocomplete="organization" maxlength="160">
			</div>
		</div>
		<div class="ve-row">
			<div class="ve-field">
				<label for="ve-email"><?php esc_html_e( 'Email', 'vinhetas-enquiries' ); ?> <span class="ve-req" aria-hidden="true">*</span></label>
				<input id="ve-email" name="ve_email" type="email" autocomplete="email" required maxlength="160">
			</div>
			<div class="ve-field">
				<label for="ve-type"><?php esc_html_e( 'You are', 'vinhetas-enquiries' ); ?></label>
				<select id="ve-type" name="ve_type">
					<?php foreach ( ve_types() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="ve-field">
			<label for="ve-message"><?php esc_html_e( 'Message', 'vinhetas-enquiries' ); ?></label>
			<textarea id="ve-message" name="ve_message" maxlength="4000"></textarea>
		</div>
		<?php if ( ve_turnstile_ready() ) : ?>
			<input type="hidden" name="ve_ts_token" value="">
			<div class="ve-ts"></div>
			<?php ve_turnstile_script(); ?>
		<?php endif; ?>
		<div class="ve-hp" aria-hidden="true">
			<label for="ve-website">Website</label>
			<input id="ve-website" name="ve_website" type="text" tabindex="-1" autocomplete="off">
		</div>
		<label class="ve-consent">
			<input type="checkbox" name="ve_consent" value="1" required>
			<span><?php esc_html_e( 'I agree that Vinhetas Algarve may store these details to reply to me about the project. We never share them and delete them on request.', 'vinhetas-enquiries' ); ?></span>
		</label>
		<p><button class="v-button" type="submit"><?php esc_html_e( 'Send', 'vinhetas-enquiries' ); ?></button></p>
	</form>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'vinhetas_enquiry', 've_shortcode' );

/**
 * Redirect back to the form with a status flag.
 *
 * @param string $status Status key.
 */
function ve_back( $status ) {
	$return = isset( $_POST['ve_return'] ) ? esc_url_raw( wp_unslash( $_POST['ve_return'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$return = wp_validate_redirect( $return, home_url( '/' ) );
	wp_safe_redirect( add_query_arg( 'enquiry', $status, $return ) . '#contact' );
	exit;
}

/**
 * Handle a submission.
 */
function ve_handle_submit() {
	if ( ! isset( $_POST['ve_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ve_nonce'] ) ), 've_submit' ) ) {
		ve_back( 'expired' );
	}

	// Bots: filled the hidden field, or submitted faster than a person could. Pretend success.
	$ts = isset( $_POST['ve_ts'] ) ? absint( $_POST['ve_ts'] ) : 0;
	if ( ! empty( $_POST['ve_website'] ) || ( time() - $ts ) < 3 ) {
		ve_back( 'sent' );
	}

	// Light rate limit. The address is hashed for a one-hour counter and never stored.
	$remote = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key    = 've_rl_' . substr( wp_hash( $remote ), 0, 20 );
	$count  = (int) get_transient( $key );
	if ( $count >= 5 ) {
		ve_back( 'busy' );
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$name    = isset( $_POST['ve_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ve_name'] ) ) : '';
	$org     = isset( $_POST['ve_org'] ) ? sanitize_text_field( wp_unslash( $_POST['ve_org'] ) ) : '';
	$email   = isset( $_POST['ve_email'] ) ? sanitize_email( wp_unslash( $_POST['ve_email'] ) ) : '';
	$type    = isset( $_POST['ve_type'] ) ? sanitize_key( wp_unslash( $_POST['ve_type'] ) ) : 'other';
	$message = isset( $_POST['ve_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ve_message'] ) ) : '';
	$consent = ! empty( $_POST['ve_consent'] );

	if ( '' === $name || ! is_email( $email ) || ! $consent ) {
		ve_back( 'missing' );
	}

	// Turnstile: a token that fails is a bot, dropped quietly. No token at all (script
	// blocked, Cloudflare down) is kept but marked unverified, so no real enquiry is lost.
	$unverified = false;
	if ( ve_turnstile_ready() ) {
		$token = isset( $_POST['ve_ts_token'] ) ? sanitize_text_field( wp_unslash( $_POST['ve_ts_token'] ) ) : '';
		if ( '' === $token ) {
			$unverified = true;
		} elseif ( ! ve_turnstile_ok( $token ) ) {
			ve_back( 'sent' );
		}
	}
	if ( ! array_key_exists( $type, ve_types() ) ) {
		$type = 'other';
	}

	$name    = mb_substr( $name, 0, 120 );
	$org     = mb_substr( $org, 0, 160 );
	$message = mb_substr( $message, 0, 4000 );

	$post_id = wp_insert_post(
		array(
			'post_type'   => VE_POST_TYPE,
			'post_status' => 'private',
			'post_title'  => ( $unverified ? '[Unverified] ' : '' ) . ( $org ? $name . ' · ' . $org : $name ),
			'meta_input'  => array(
				've_name'       => $name,
				've_org'        => $org,
				've_email'      => $email,
				've_type'       => $type,
				've_message'    => $message,
				've_consent_at' => gmdate( 'c' ),
			),
		),
		true
	);

	if ( ! is_wp_error( $post_id ) ) {
		$types = ve_types();
		$body  = sprintf(
			"New enquiry on vinhetas.pt\n\nName: %s\nOrganisation: %s\nEmail: %s\nType: %s\n\n%s\n\nView: %s",
			$name,
			$org ? $org : '-',
			$email,
			$types[ $type ],
			$message ? $message : '(no message)',
			admin_url( 'post.php?post=' . $post_id . '&action=edit' )
		);
		wp_mail(
			ve_notify_to(),
			/* translators: %s: enquirer name */
			sprintf( __( 'Vinhetas enquiry: %s', 'vinhetas-enquiries' ), $name ),
			$body,
			array( 'Reply-To: ' . $name . ' <' . $email . '>' )
		);
	}

	ve_back( 'sent' );
}
add_action( 'admin_post_nopriv_ve_submit', 've_handle_submit' );
add_action( 'admin_post_ve_submit', 've_handle_submit' );

/**
 * Where notifications go: Settings → General → "Enquiry notifications", else the site admin email.
 *
 * @return string
 */
function ve_notify_to() {
	$to = get_option( 've_notify_email', '' );
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

/**
 * Settings field for the notification address.
 */
function ve_settings() {
	register_setting( 'general', 've_notify_email', array( 'sanitize_callback' => 'sanitize_email' ) );
	add_settings_field(
		've_notify_email',
		__( 'Enquiry notifications', 'vinhetas-enquiries' ),
		function () {
			printf(
				'<input type="email" class="regular-text" name="ve_notify_email" value="%s" placeholder="%s"><p class="description">%s</p>',
				esc_attr( get_option( 've_notify_email', '' ) ),
				esc_attr( get_option( 'admin_email' ) ),
				esc_html__( 'Where new partner enquiries are emailed. Leave empty to use the administration email.', 'vinhetas-enquiries' )
			);
		},
		'general'
	);
}
add_action( 'admin_init', 've_settings' );

/**
 * Admin list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function ve_columns( $columns ) {
	return array(
		'cb'       => $columns['cb'],
		'title'    => __( 'From', 'vinhetas-enquiries' ),
		've_email' => __( 'Email', 'vinhetas-enquiries' ),
		've_type'  => __( 'Type', 'vinhetas-enquiries' ),
		'date'     => __( 'Received', 'vinhetas-enquiries' ),
	);
}
add_filter( 'manage_' . VE_POST_TYPE . '_posts_columns', 've_columns' );

/**
 * Admin list column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function ve_column_value( $column, $post_id ) {
	if ( 've_email' === $column ) {
		$email = get_post_meta( $post_id, 've_email', true );
		printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
	} elseif ( 've_type' === $column ) {
		$types = ve_types();
		$type  = get_post_meta( $post_id, 've_type', true );
		echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type );
	}
}
add_action( 'manage_' . VE_POST_TYPE . '_posts_custom_column', 've_column_value', 10, 2 );

/**
 * Read-only details box on the enquiry screen.
 */
function ve_meta_box() {
	add_meta_box(
		've_details',
		__( 'Enquiry', 'vinhetas-enquiries' ),
		function ( $post ) {
			$types = ve_types();
			$type  = get_post_meta( $post->ID, 've_type', true );
			$rows  = array(
				__( 'Name', 'vinhetas-enquiries' )         => get_post_meta( $post->ID, 've_name', true ),
				__( 'Organisation', 'vinhetas-enquiries' ) => get_post_meta( $post->ID, 've_org', true ),
				__( 'Email', 'vinhetas-enquiries' )        => get_post_meta( $post->ID, 've_email', true ),
				__( 'Type', 'vinhetas-enquiries' )         => isset( $types[ $type ] ) ? $types[ $type ] : $type,
				__( 'Consent given', 'vinhetas-enquiries' ) => get_post_meta( $post->ID, 've_consent_at', true ),
			);
			echo '<table class="form-table" role="presentation"><tbody>';
			foreach ( $rows as $label => $value ) {
				printf( '<tr><th scope="row">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ? $value : '-' ) );
			}
			echo '</tbody></table>';
			echo '<h3>' . esc_html__( 'Message', 'vinhetas-enquiries' ) . '</h3>';
			echo '<div style="white-space:pre-wrap;max-width:720px">' . esc_html( get_post_meta( $post->ID, 've_message', true ) ) . '</div>';
		},
		VE_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 've_meta_box' );

/**
 * "Download CSV" button above the list.
 *
 * @param string $which Top or bottom.
 */
function ve_export_button( $which ) {
	global $typenow;
	if ( VE_POST_TYPE !== $typenow || 'top' !== $which || ! current_user_can( 'edit_others_posts' ) ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=ve_export' ), 've_export' );
	printf( '<div class="alignleft actions"><a class="button" href="%s">%s</a></div>', esc_url( $url ), esc_html__( 'Download CSV', 'vinhetas-enquiries' ) );
}
add_action( 'manage_posts_extra_tablenav', 've_export_button' );

/**
 * CSV export.
 */
function ve_export() {
	if ( ! current_user_can( 'edit_others_posts' ) || ! check_admin_referer( 've_export' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'vinhetas-enquiries' ) );
	}

	$posts = get_posts(
		array(
			'post_type'      => VE_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=vinhetas-enquiries-' . gmdate( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // BOM so Excel and Numbers read the accents.
	fputcsv( $out, array( 'Received', 'Name', 'Organisation', 'Email', 'Type', 'Message', 'Consent' ) );
	$types = ve_types();
	foreach ( $posts as $p ) {
		$type = get_post_meta( $p->ID, 've_type', true );
		$row  = array(
			get_the_date( 'Y-m-d H:i', $p ),
			get_post_meta( $p->ID, 've_name', true ),
			get_post_meta( $p->ID, 've_org', true ),
			get_post_meta( $p->ID, 've_email', true ),
			isset( $types[ $type ] ) ? $types[ $type ] : $type,
			get_post_meta( $p->ID, 've_message', true ),
			get_post_meta( $p->ID, 've_consent_at', true ),
		);
		// Stop spreadsheet formula injection.
		$row = array_map(
			function ( $v ) {
				$v = (string) $v;
				return ( '' !== $v && in_array( $v[0], array( '=', '+', '-', '@' ), true ) ) ? "'" . $v : $v;
			},
			$row
		);
		fputcsv( $out, $row );
	}
	fclose( $out );
	exit;
}
add_action( 'admin_post_ve_export', 've_export' );
