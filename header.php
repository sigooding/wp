<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$account = nexora_trade_account_urls();
$topbar_text = get_theme_mod( 'nexora_show_topbar', false ) ? get_theme_mod( 'nexora_topbar_text', '' ) : '';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<?php if ( $topbar_text ) : ?>
  <div class="portal-topbar"><div class="shell"><span><?php echo esc_html( $topbar_text ); ?></span><?php if ( get_theme_mod( 'nexora_topbar_url', '' ) ) : ?><a href="<?php echo esc_url( get_theme_mod( 'nexora_topbar_url' ) ); ?>"><?php echo esc_html( get_theme_mod( 'nexora_topbar_link_text', 'Read the announcement' ) ); ?> →</a><?php endif; ?></div></div>
<?php endif; ?>
<header class="site-header<?php echo get_theme_mod( 'nexora_sticky_header', false ) ? ' site-header-sticky' : ''; ?>">
  <div class="shell header-top">
    <div class="header-brand-group">
      <div class="site-brand"><?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?><a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="brand-symbol"><svg viewBox="0 0 40 40" fill="none" aria-hidden="true"><path d="M5.5 17.5 20 5l14.5 12.5v15H23.7v-9h-7.4v9H5.5v-15Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M13 14.7 20 8.8l7 5.9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg></span><span class="brand-name"><strong><?php echo esc_html( get_bloginfo( 'name' ) ?: 'aqara' ); ?></strong><small><?php echo esc_html( get_theme_mod( 'nexora_brand_label', 'SMART LIVING' ) ); ?></small></span></a><?php } ?></div>
      <?php if ( get_theme_mod( 'nexora_portal_label', 'TRADE PARTNER PORTAL' ) ) : ?><div class="header-divider"></div><div class="portal-name"><?php echo esc_html( get_theme_mod( 'nexora_portal_label', 'TRADE PARTNER PORTAL' ) ); ?></div><?php endif; ?>
    </div>
    <div class="header-actions">
      <?php if ( get_theme_mod( 'nexora_show_search', true ) ) : ?><form role="search" method="get" class="header-search" action="<?php echo esc_url( home_url( '/' ) ); ?>"><span aria-hidden="true">⌕</span><input type="search" name="s" placeholder="<?php echo esc_attr( get_theme_mod( 'nexora_search_placeholder', 'Search the portal...' ) ); ?>" aria-label="<?php esc_attr_e( 'Search the portal', 'nexora-trade' ); ?>"><button type="submit" aria-label="<?php esc_attr_e( 'Search', 'nexora-trade' ); ?>">→</button></form><?php endif; ?>
      <?php if ( get_theme_mod( 'nexora_show_support', true ) ) : ?><a class="header-help" href="<?php echo esc_url( nexora_trade_url( 'support' ) ); ?>"><span class="round-icon" aria-hidden="true">?</span><span><small>NEED A HAND?</small><strong><?php echo esc_html( get_theme_mod( 'nexora_help_label', 'Partner support' ) ); ?></strong></span></a><?php endif; ?>
      <?php if ( get_theme_mod( 'nexora_show_account', true ) ) : ?><a class="account-link" href="<?php echo esc_url( $account['account'] ); ?>"><span class="round-icon" aria-hidden="true">♙</span><span><small>YOUR PORTAL</small><strong><?php echo esc_html( get_theme_mod( 'nexora_account_label', 'My Account' ) ); ?></strong></span></a><a class="logout-link" href="<?php echo esc_url( $account['switch'] ); ?>"><?php echo esc_html( $account['switch_label'] ); ?></a><?php endif; ?>
      <?php if ( ! nexora_megamenu_controls_navigation() ) : ?><button class="menu-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e( 'Toggle navigation', 'nexora-trade' ); ?>">☰</button><?php endif; ?>
    </div>
  </div>
  <div class="navigation-bar<?php echo nexora_megamenu_controls_navigation() ? ' navigation-open' : ''; ?>" id="primary-nav"><div class="shell nav-inner"><nav aria-label="<?php esc_attr_e( 'Main navigation', 'nexora-trade' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'portal-nav', 'fallback_cb' => 'nexora_trade_primary_fallback' ) ); ?>
    <?php if ( get_theme_mod( 'nexora_show_account', true ) ) : ?><div class="mobile-account-links"><a href="<?php echo esc_url( $account['account'] ); ?>"><?php echo esc_html( get_theme_mod( 'nexora_account_label', 'My Account' ) ); ?></a><a href="<?php echo esc_url( $account['switch'] ); ?>"><?php echo esc_html( $account['switch_label'] ); ?></a></div><?php endif; ?>
    <?php if ( get_theme_mod( 'nexora_show_search', true ) ) : ?><form role="search" method="get" class="mobile-portal-search" action="<?php echo esc_url( home_url( '/' ) ); ?>"><input type="search" name="s" placeholder="<?php echo esc_attr( get_theme_mod( 'nexora_search_placeholder', 'Search the portal...' ) ); ?>" aria-label="<?php esc_attr_e( 'Search the portal', 'nexora-trade' ); ?>"><button type="submit"><?php esc_html_e( 'Search', 'nexora-trade' ); ?></button></form><?php endif; ?>
  </nav><?php if ( get_theme_mod( 'nexora_header_note', '' ) ) : ?><span class="nav-note"><i></i> <?php echo esc_html( get_theme_mod( 'nexora_header_note', 'YOUR PARTNER IN SMARTER LIVING' ) ); ?></span><?php endif; ?></div></div>
</header>
