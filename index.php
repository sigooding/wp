<?php
get_header();
$layout = 'full' === nexora_sanitize_layout( get_theme_mod( 'nexora_page_layout', 'constrained' ) ) ? ' content-page-full' : '';
$show_dates = get_theme_mod( 'nexora_show_post_dates', true );
?>
<main class="shell content-page<?php echo esc_attr( $layout ); ?>">
  <?php if ( is_search() ) : ?>
    <h1><?php printf( esc_html__( 'Search results for: %s', 'nexora-trade' ), esc_html( get_search_query() ) ); ?></h1>
  <?php elseif ( is_home() ) : ?>
    <h1><?php esc_html_e( 'News & updates', 'nexora-trade' ); ?></h1>
  <?php elseif ( is_archive() ) : ?>
    <h1><?php the_archive_title(); ?></h1>
    <?php the_archive_description( '<div class="entry-content">', '</div>' ); ?>
  <?php endif; ?>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php if ( is_singular() ) : ?>
      <div class="breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nexora-trade' ); ?></a> / <?php the_title(); ?></div>
    <?php endif; ?>
    <article <?php post_class( 'post-card' ); ?>>
      <?php if ( $show_dates ) : ?><p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p><?php endif; ?>
      <?php if ( is_singular() ) : ?>
        <h1><?php the_title(); ?></h1>
      <?php else : ?>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <?php endif; ?>
      <div class="entry-content"><?php if ( is_singular() ) { the_content(); wp_link_pages(); } else { the_excerpt(); } ?></div>
      <?php if ( ! is_singular() ) : ?><a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'nexora-trade' ); ?> →</a><?php endif; ?>
    </article>
  <?php endwhile; the_posts_navigation(); else : ?>
    <div class="post-card"><h2><?php esc_html_e( 'Nothing found', 'nexora-trade' ); ?></h2><p><?php esc_html_e( 'Try another search or explore the portal.', 'nexora-trade' ); ?></p><?php get_search_form(); ?></div>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
