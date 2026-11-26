<?php
/**
 * Front page: renders Elementor/editor content when present, otherwise the
 * theme's built-in portal homepage (with optional Smart Slider 3 hero).
 */
get_header();
$front_id = get_queried_object_id();
$elementor_document = is_page() && class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::$instance->documents->get( $front_id ) : false;
$elementor_page = $elementor_document && $elementor_document->is_built_with_elementor();
$page_content = is_page() ? trim( (string) get_post_field( 'post_content', $front_id ) ) : '';

if ( is_page() && ( $elementor_page || '' !== $page_content ) ) :
    while ( have_posts() ) : the_post(); ?>
        <main class="full-width-page"><?php the_content(); ?></main>
    <?php endwhile;
else :
    $hero = get_theme_mod( 'nexora_hero_image', get_template_directory_uri() . '/assets/portal-hero.jpg' );
    if ( ! $hero ) {
        $hero = get_template_directory_uri() . '/assets/portal-hero.jpg';
    }
    $products_url = nexora_trade_url( 'products' );
    $hero_slider  = nexora_hero_slider();
    $tiles        = array(
        array( '01', '▣', 'Explore Products', 'Browse our complete product range', $products_url ),
        array( '02', '▤', 'Technical Documents', 'Find specifications and manuals', nexora_trade_url( 'technical' ) ),
        array( '03', '▧', 'Marketing Assets', 'Download images and brochures', nexora_trade_url( 'marketing' ) ),
        array( '04', '▥', 'Training Centre', 'Learn about products and installation', nexora_trade_url( 'training' ) ),
    );
    $featured     = array(
        array( 'PRODUCT CATALOGUE', 'Product catalogue 2026', '2026 edition', get_theme_mod( 'nexora_catalogue_url' ), 'product-hub.jpg', 'THE PRODUCT COLLECTION' ),
        array( 'INSTALLATION GUIDE', 'Smart home installation guide', 'Technical document', get_theme_mod( 'nexora_installation_url' ), 'installation-guide.jpg', 'INSTALLATION GUIDE' ),
        array( 'MARKETING BROCHURE', 'Latest marketing brochure', 'Marketing resource', get_theme_mod( 'nexora_brochure_url' ), 'portal-hero.jpg', 'LIVE A LITTLE SMARTER' ),
    );
    $posts = get_posts( array( 'numberposts' => 3, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) );
    ?>
    <main class="shell home-main">
        <?php if ( $hero_slider ) : ?>
            <section class="hero-slider" aria-label="<?php esc_attr_e( 'Featured', 'nexora-trade' ); ?>"><?php echo $hero_slider; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- slider shortcode is sanitised in the Customizer ?></section>
        <?php else : ?>
            <section class="hero" style="background-image:linear-gradient(90deg,rgba(9,48,40,.96) 0%,rgba(11,52,44,.9) 26%,rgba(11,52,44,.64) 47%,rgba(11,52,44,.08) 75%),url('<?php echo esc_url( $hero ); ?>')">
                <div class="hero-content"><p class="hero-eyebrow"><span></span> THE PARTNER PORTAL</p><h1><?php echo esc_html( get_theme_mod( 'nexora_hero_heading', 'Welcome to your Trade Partner Portal.' ) ); ?></h1><p class="hero-description"><?php echo esc_html( get_theme_mod( 'nexora_hero_description', 'Everything you need to promote, distribute and support our smart home product range.' ) ); ?></p><div class="hero-actions"><a class="button button-light" href="<?php echo esc_url( $products_url ); ?>">Explore products <b>→</b></a><a class="button button-ghost" href="<?php echo esc_url( nexora_trade_url( 'downloads' ) ); ?>">Browse resources <b>→</b></a></div></div><div class="hero-caption"><span>✧</span><div><small>SMARTER SPACES</small><strong>Better living, together.</strong></div></div>
            </section>
        <?php endif; ?>
        <section class="toolkit-section"><div class="section-heading"><div><p class="eyebrow"><i></i> YOUR PARTNER TOOLKIT</p><h2>Everything you need, all in one place.</h2><p>Quick access to the tools and resources that help you do more.</p></div><a class="text-link" href="<?php echo esc_url( nexora_trade_url( 'downloads' ) ); ?>">Explore all resources →</a></div><div class="tool-grid"><?php foreach ( $tiles as $tile ) : ?><a class="tool-card" href="<?php echo esc_url( $tile[4] ); ?>"><div class="tool-card-top"><span class="tool-icon"><?php echo esc_html( $tile[1] ); ?></span><span class="tool-number"><?php echo esc_html( $tile[0] ); ?></span></div><div class="tool-card-bottom"><div><h3><?php echo esc_html( $tile[2] ); ?></h3><p><?php echo esc_html( $tile[3] ); ?></p></div><span class="card-arrow">↗</span></div></a><?php endforeach; ?></div></section>
        <div class="home-columns">
            <section><div class="section-heading"><div><p class="eyebrow"><i></i> THE LATEST</p><h2>Latest announcements</h2><p>Stay in the know with news from our team.</p></div><a class="text-link" href="<?php echo esc_url( nexora_trade_url( 'news' ) ); ?>">View all news →</a></div><div class="updates-panel">
                <?php if ( $posts ) : foreach ( $posts as $post_item ) : ?><a class="announcement-row" href="<?php echo esc_url( get_permalink( $post_item ) ); ?>"><span class="date-tile"><strong><?php echo esc_html( get_the_date( 'd', $post_item ) ); ?></strong><small><?php echo esc_html( strtoupper( get_the_date( 'M', $post_item ) ) ); ?></small></span><span class="announcement-copy"><small>PARTNER NEWS</small><strong><?php echo esc_html( get_the_title( $post_item ) ); ?></strong><em><?php echo esc_html( wp_trim_words( $post_item->post_excerpt ?: $post_item->post_content, 12 ) ); ?></em></span><span class="row-arrow">↗</span></a><?php endforeach; else : foreach ( array( 'New smart home products launched', 'Updated installation documentation', 'Latest firmware available' ) as $headline ) : ?><a class="announcement-row" href="<?php echo esc_url( nexora_trade_url( 'news' ) ); ?>"><span class="date-tile"><strong>•</strong><small>NEWS</small></span><span class="announcement-copy"><small>PARTNER NEWS</small><strong><?php echo esc_html( $headline ); ?></strong><em>Visit the news section for the latest updates.</em></span><span class="row-arrow">↗</span></a><?php endforeach; endif; ?>
            </div></section>
            <section><div class="section-heading"><div><p class="eyebrow"><i></i> HAND-PICKED FOR YOU</p><h2>Featured resources</h2><p>The essentials, ready to download.</p></div><a class="text-link" href="<?php echo esc_url( nexora_trade_url( 'downloads' ) ); ?>">View all downloads →</a></div><div class="resources-panel"><?php foreach ( $featured as $resource ) : $url = $resource[3] ? $resource[3] : nexora_trade_url( 'downloads' ); ?><a class="resource-row" href="<?php echo esc_url( $url ); ?>"><span class="resource-cover" style="background-image:linear-gradient(180deg,rgba(13,35,30,.12),rgba(11,30,26,.8)),url('<?php echo esc_url( get_template_directory_uri() . '/assets/' . $resource[4] ); ?>')"><small>PARTNER</small><strong><?php echo esc_html( $resource[5] ); ?></strong></span><span class="resource-copy"><small><?php echo esc_html( $resource[0] ); ?></small><strong><?php echo esc_html( $resource[1] ); ?></strong><em><?php echo esc_html( $resource[2] ); ?></em></span><span class="download-icon">↓</span></a><?php endforeach; ?></div></section>
        </div>
        <section class="support-banner"><div class="support-icon">✳</div><div><p class="eyebrow">WE’RE HERE FOR YOU</p><h2>Need a hand? Let’s talk.</h2><p>Whatever you’re working on, our team is here to help.</p></div><a class="button button-dark" href="<?php echo esc_url( nexora_trade_url( 'support' ) ); ?>">Contact support <b>→</b></a></section><div class="home-bottom-note">Your resources, all in one place. <span>BUILT FOR BETTER PARTNERSHIPS</span></div>
    </main>
<?php endif; get_footer(); ?>
