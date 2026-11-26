<?php
get_header();
$layout = 'full' === nexora_sanitize_layout( get_theme_mod( 'nexora_page_layout', 'constrained' ) ) ? ' content-page-full' : '';
?>
<main class="shell content-page<?php echo esc_attr( $layout ); ?>">
  <article class="post-card nexora-404">
    <p class="eyebrow">ERROR 404</p>
    <h1><?php echo esc_html( get_theme_mod( 'nexora_404_heading', 'We couldn’t find that page.' ) ); ?></h1>
    <p><?php echo esc_html( get_theme_mod( 'nexora_404_text', 'The link may have changed. Try the portal navigation or search below.' ) ); ?></p>
    <div class="nexora-404-links">
      <a class="button button-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_theme_mod( 'nexora_404_button', 'Back to the portal' ) ); ?> →</a>
      <a class="button button-outline" href="<?php echo esc_url( nexora_trade_url( 'products' ) ); ?>">Browse products</a>
      <a class="button button-outline" href="<?php echo esc_url( nexora_trade_url( 'support' ) ); ?>">Contact support</a>
    </div>
    <?php get_search_form(); ?>
  </article>
</main>
<?php get_footer(); ?>
