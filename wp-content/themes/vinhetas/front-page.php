<?php
/**
 * Home page: the Vinhetas care campus.
 *
 * @package vinhetas
 */

get_header();
?>
<main id="main" class="v-main">

	<section class="v-hero" aria-labelledby="v-hero-title">
		<div class="v-hero__media">
			<?php vinhetas_media( 'vinhetas_hero', 'vinhetas-hero', __( 'Photo · View from the land', 'vinhetas' ), '16 / 9', __( 'Landscape 16:9, at least 2400 × 1350 px', 'vinhetas' ) ); ?>
		</div>
		<div class="v-hero__text v-wrap">
			<p class="v-kicker"><?php esc_html_e( 'Poço Barreto · Silves · Algarve, Portugal', 'vinhetas' ); ?></p>
			<h1 id="v-hero-title" class="v-hero__title"><?php esc_html_e( 'A private care campus in the Algarve countryside', 'vinhetas' ); ?></h1>
			<p class="v-lead">
				<?php
				echo wp_kses(
					__( 'Vinhetas Algarve is planning a <strong>100% private</strong> campus for memory care, assisted living and a small hospice and rehabilitation wing, with family suites and staff housing, built in phases. We are looking for an <strong>operating partner</strong> to shape the programme now, before the planning request is filed.', 'vinhetas' ),
					array( 'strong' => array() )
				);
				?>
			</p>
			<p><a class="v-button" href="#contact"><?php esc_html_e( 'Start a conversation', 'vinhetas' ); ?></a></p>
		</div>
	</section>

	<section class="v-facts" aria-label="<?php esc_attr_e( 'Key facts', 'vinhetas' ); ?>">
		<div class="v-grid v-grid--3">
			<div class="v-cell">
				<h2 class="v-label"><?php esc_html_e( 'Location', 'vinhetas' ); ?></h2>
				<ul class="v-list">
					<li><?php esc_html_e( '10–15 min to the beach at Armação de Pêra', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( '25 min to Portimão and Albufeira', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( '45 min to Faro Airport', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'Beside the Algarve rail line, walking distance to Poço Barreto station', 'vinhetas' ); ?></li>
				</ul>
			</div>
			<div class="v-cell">
				<h2 class="v-label"><?php esc_html_e( 'Site', 'vinhetas' ); ?></h2>
				<ul class="v-list">
					<li><?php esc_html_e( 'About 8 ha of rural land held by the founders', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'Oaks, olives and open countryside', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'Existing registered house with water, sewer and power; a well and a large cistern', 'vinhetas' ); ?></li>
				</ul>
			</div>
			<div class="v-cell">
				<h2 class="v-label"><?php esc_html_e( 'Programme', 'vinhetas' ); ?></h2>
				<ul class="v-list">
					<li><?php esc_html_e( 'Memory care in small households', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'Assisted living, with suites for couples', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'Private hospice and rehabilitation wing', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'No public-network beds', 'vinhetas' ); ?></li>
				</ul>
			</div>
		</div>
	</section>

	<section class="v-stats" aria-labelledby="v-stats-title">
		<h2 id="v-stats-title" class="v-sr"><?php esc_html_e( 'Why Silves', 'vinhetas' ); ?></h2>
		<div class="v-grid v-grid--4">
			<div class="v-stat"><span class="v-stat__num">24.8%</span><span class="v-stat__text"><?php esc_html_e( 'of Silves residents are aged 65+', 'vinhetas' ); ?></span></div>
			<div class="v-stat"><span class="v-stat__num">97%</span><span class="v-stat__text"><?php esc_html_e( 'occupancy across Silves’ ~260 care-home places', 'vinhetas' ); ?></span></div>
			<div class="v-stat"><span class="v-stat__num">€1,852</span><span class="v-stat__text"><?php esc_html_e( 'average monthly private care-home fee in Portugal, 2026', 'vinhetas' ); ?></span></div>
			<div class="v-stat"><span class="v-stat__num">€3.5–6k</span><span class="v-stat__text"><?php esc_html_e( 'monthly fees at premium English-speaking Algarve homes', 'vinhetas' ); ?></span></div>
		</div>
		<p class="v-sources v-wrap"><?php esc_html_e( 'Sources: Câmara Municipal de Silves planning documents (2024); GEE municipal statistics (2023); Lares Online (2026); adviser market guides (2026).', 'vinhetas' ); ?></p>
	</section>

	<section id="campus" class="v-section" aria-labelledby="v-campus-title">
		<div class="v-wrap">
			<p class="v-num">01</p>
			<h2 id="v-campus-title" class="v-h2"><?php esc_html_e( 'The campus, built in phases', 'vinhetas' ); ?></h2>
		</div>
		<div class="v-rendering v-wrap">
			<?php vinhetas_media( 'vinhetas_rendering', 'vinhetas-wide', __( 'Concept rendering · Campus', 'vinhetas' ), '16 / 9', __( 'Landscape 16:9, at least 1800 × 1013 px', 'vinhetas' ) ); ?>
			<p class="v-caption"><?php esc_html_e( 'Illustrative only. The final layout will be set with the operator and through the planning process.', 'vinhetas' ); ?></p>
		</div>
		<div class="v-grid v-grid--3 v-grid--ruled">
			<div class="v-cell">
				<h3 class="v-label v-label--oak"><?php esc_html_e( 'Phase 1', 'vinhetas' ); ?></h3>
				<p><?php esc_html_e( 'About 60 beds of memory care in small households, a 10–15-bed private hospice and rehabilitation wing, family suites and staff housing.', 'vinhetas' ); ?></p>
			</div>
			<div class="v-cell">
				<h3 class="v-label v-label--oak"><?php esc_html_e( 'Phase 2', 'vinhetas' ); ?></h3>
				<p><?php esc_html_e( 'A second home of about 60 beds for assisted living, with suites for couples, once Phase 1 is established.', 'vinhetas' ); ?></p>
			</div>
			<div class="v-cell">
				<h3 class="v-label v-label--oak"><?php esc_html_e( 'Reserved', 'vinhetas' ); ?></h3>
				<p><?php esc_html_e( 'Land held in the plan for a larger clinical wing or other care uses, subject to demand and planning.', 'vinhetas' ); ?></p>
			</div>
		</div>
	</section>

	<section id="site" class="v-section" aria-labelledby="v-site-title">
		<div class="v-wrap">
			<p class="v-num">02</p>
			<h2 id="v-site-title" class="v-h2"><?php esc_html_e( 'The land', 'vinhetas' ); ?></h2>
			<p class="v-lead v-lead--narrow"><?php esc_html_e( 'Rolling rural land of oaks and olives between Silves and the coast, minutes from the beaches of Armação de Pêra and beside the Algarve rail line, which gives staff an easy commute.', 'vinhetas' ); ?></p>
		</div>
		<div class="v-gallery v-wrap">
			<?php
			for ( $i = 1; $i <= 3; $i++ ) {
				echo '<div class="v-gallery__item">';
				/* translators: %d: photo number */
				vinhetas_media( 'vinhetas_site_' . $i, 'vinhetas-square', sprintf( __( 'Site photo %d', 'vinhetas' ), $i ), '1 / 1', __( 'Square, at least 900 × 900 px', 'vinhetas' ) );
				echo '</div>';
			}
			?>
		</div>
	</section>

	<section id="planning" class="v-section" aria-labelledby="v-planning-title">
		<div class="v-wrap">
			<p class="v-num">03</p>
			<h2 id="v-planning-title" class="v-h2"><?php esc_html_e( 'Planning route', 'vinhetas' ); ?></h2>
			<p class="v-lead v-lead--narrow"><?php esc_html_e( 'Silves has used this route, a detailed rural plan (PIER) under a planning contract, for an elder-care campus on agricultural land, started by unanimous vote in 2024. Operator input shapes the plan before it is filed.', 'vinhetas' ); ?></p>
		</div>
		<ol class="v-steps">
			<li class="v-step is-current"><span class="v-step__num">01</span><span><?php esc_html_e( 'Informal soundings with Silves and agencies', 'vinhetas' ); ?> <strong><?php esc_html_e( 'Now', 'vinhetas' ); ?></strong></span></li>
			<li class="v-step"><span class="v-step__num">02</span><span><?php esc_html_e( 'Planning contract and terms of reference', 'vinhetas' ); ?></span></li>
			<li class="v-step"><span class="v-step__num">03</span><span><?php esc_html_e( 'Plan preparation, agency review, public consultation', 'vinhetas' ); ?></span></li>
			<li class="v-step"><span class="v-step__num">04</span><span><?php esc_html_e( 'Plan approved and published', 'vinhetas' ); ?></span></li>
			<li class="v-step"><span class="v-step__num">05</span><span><?php esc_html_e( 'Design, licensing and construction', 'vinhetas' ); ?></span></li>
		</ol>
	</section>

	<section class="v-section v-section--split" aria-label="<?php esc_attr_e( 'Partners and founders', 'vinhetas' ); ?>">
		<div class="v-grid v-grid--2 v-grid--ruled">
			<div class="v-cell">
				<h2 class="v-label"><?php esc_html_e( 'What we offer an operator', 'vinhetas' ); ?></h2>
				<ul class="v-list v-list--spaced">
					<li><?php echo wp_kses( __( '<strong>A blank sheet.</strong> Your care model, household sizes and room layouts go into the plan before it is filed.', 'vinhetas' ), array( 'strong' => array() ) ); ?></li>
					<li><?php echo wp_kses( __( '<strong>Built for a premium site.</strong> Family suites and staff housing on campus; rail access for staff.', 'vinhetas' ), array( 'strong' => array() ) ); ?></li>
					<li><?php echo wp_kses( __( '<strong>Landscape.</strong> Oaks, olives and countryside, minutes from the coast.', 'vinhetas' ), array( 'strong' => array() ) ); ?></li>
					<li><?php echo wp_kses( __( '<strong>Flexible terms.</strong> Lease, management contract, joint venture, or a long-term land lease (direito de superfície).', 'vinhetas' ), array( 'strong' => array() ) ); ?></li>
				</ul>
			</div>
			<div class="v-cell">
				<h2 class="v-label"><?php esc_html_e( 'Who we are', 'vinhetas' ); ?></h2>
				<p><?php esc_html_e( 'Vinhetas Algarve S.A. was founded in 2026 by Katrina Smith-Adair and Hagan Blount, whose background is real estate, boutique hospitality, private dining and event production.', 'vinhetas' ); ?></p>
				<p><?php esc_html_e( 'We are investors, developers and hosts who love to bring interesting ideas and people together.', 'vinhetas' ); ?></p>
			</div>
		</div>
	</section>

	<section id="contact" class="v-contact" aria-labelledby="v-contact-title">
		<div class="v-contact__inner">
			<div class="v-contact__intro">
				<h2 id="v-contact-title" class="v-h2 v-h2--light"><?php esc_html_e( 'Partner with us', 'vinhetas' ); ?></h2>
				<p><?php esc_html_e( 'We would like to hear from care operators, healthcare groups and investors. What we are asking for:', 'vinhetas' ); ?></p>
				<ol class="v-asks">
					<li><?php esc_html_e( 'A 30-minute introductory call', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'Your view of the right programme and phasing for this site', 'vinhetas' ); ?></li>
					<li><?php esc_html_e( 'If there is a fit, a non-binding letter of interest to go with our planning request', 'vinhetas' ); ?></li>
				</ol>
			</div>
			<div class="v-contact__form">
				<?php vinhetas_enquiry_form(); ?>
			</div>
		</div>
	</section>

</main>
<?php
get_footer();
