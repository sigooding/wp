<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$phone = get_theme_mod( 'nexora_footer_phone', '' );
$email = get_theme_mod( 'nexora_footer_email', '' );
$address = get_theme_mod( 'nexora_footer_address', '' );
$hours = get_theme_mod( 'nexora_footer_hours', '' );
$show_contact = get_theme_mod( 'nexora_show_footer_contact', true ) && ( $phone || $email || $address || $hours );
?>
<footer class="site-footer">
  <div class="shell footer-main">
    <div class="footer-about"><a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ?: 'nexora' ); ?></a><p><?php echo esc_html( get_theme_mod( 'nexora_footer_tagline', 'Making smarter living simpler, together.' ) ); ?></p></div>
    <div class="footer-links"><strong>EXPLORE</strong><?php if ( has_nav_menu( 'footer_explore' ) ) { wp_nav_menu( array( 'theme_location' => 'footer_explore', 'container' => false, 'menu_class' => 'nexora-footer-menu', 'depth' => 1 ) ); } else { ?><a href="<?php echo esc_url( nexora_trade_url( 'products' ) ); ?>">Products</a><a href="<?php echo esc_url( nexora_trade_url( 'technical' ) ); ?>">Technical</a><a href="<?php echo esc_url( nexora_trade_url( 'marketing' ) ); ?>">Marketing</a><?php } ?></div>
    <div class="footer-links"><strong>PARTNER RESOURCES</strong><?php if ( has_nav_menu( 'footer_resources' ) ) { wp_nav_menu( array( 'theme_location' => 'footer_resources', 'container' => false, 'menu_class' => 'nexora-footer-menu', 'depth' => 1 ) ); } else { ?><a href="<?php echo esc_url( nexora_trade_url( 'training' ) ); ?>">Training</a><a href="<?php echo esc_url( nexora_trade_url( 'downloads' ) ); ?>">Downloads</a><a href="<?php echo esc_url( nexora_trade_url( 'news' ) ); ?>">News &amp; updates</a><?php } ?></div>
    <div class="footer-links"><strong>HERE TO HELP</strong><p><?php echo esc_html( get_theme_mod( 'nexora_footer_help', 'Questions? We’re here to support you.' ) ); ?></p>
      <?php if ( $show_contact ) : ?><ul class="nexora-footer-contact"><?php if ( $phone ) : ?><li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?><?php if ( $email ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li><?php endif; ?><?php if ( $address ) : ?><li><?php echo esc_html( $address ); ?></li><?php endif; ?><?php if ( $hours ) : ?><li><?php echo esc_html( $hours ); ?></li><?php endif; ?></ul><?php endif; ?>
      <a class="footer-cta" href="<?php echo esc_url( nexora_trade_url( 'support' ) ); ?>"><?php echo esc_html( get_theme_mod( 'nexora_footer_cta', 'Contact partner support' ) ); ?> ↗</a>
    </div>
  </div>
  <div class="shell footer-bottom"><span><?php echo esc_html( nexora_trade_copyright() ); ?></span><span><?php echo esc_html( get_theme_mod( 'nexora_footer_note', 'SMARTER SPACES. BETTER LIVING.' ) ); ?></span></div>
</footer>
<?php wp_footer(); ?></body></html>
