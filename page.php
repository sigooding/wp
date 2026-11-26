<?php
get_header();
$layout = 'full' === nexora_sanitize_layout( get_theme_mod( 'nexora_page_layout', 'constrained' ) ) ? ' content-page-full' : '';
while ( have_posts() ) :
    the_post();
    // Pages built with a page builder own their own headings/layout: don't
    // print the theme title/breadcrumbs on top of them (avoids duplicate H1s).
    $built_with_elementor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor();
    $built_with_beaver    = class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'is_builder_enabled' ) && FLBuilderModel::is_builder_enabled( get_the_ID() );
    if ( $built_with_elementor || $built_with_beaver ) : ?>
        <main class="full-width-page"><?php the_content(); ?></main>
    <?php else : ?>
        <main class="shell content-page<?php echo esc_attr( $layout ); ?>">
            <div class="breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></div>
            <article <?php post_class(); ?>><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); ?></div><?php wp_link_pages(); ?></article>
        </main>
    <?php endif;
endwhile;
get_footer(); ?>
