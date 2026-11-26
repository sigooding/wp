<?php
get_header();
$layout = 'full' === nexora_sanitize_layout( get_theme_mod( 'nexora_page_layout', 'constrained' ) ) ? ' content-page-full' : '';
?>
<main class="shell content-page woocommerce-content<?php echo esc_attr( $layout ); ?>">
  <?php woocommerce_content(); ?>
</main>
<?php get_footer(); ?>
