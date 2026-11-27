<?php
/** Nexora Trade: Elementor Free-friendly classic theme with catalogue-only WooCommerce support. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nexora_trade_setup() {
    load_theme_textdomain( 'nexora-trade', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 420, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus( array(
        'primary' => __( 'Primary navigation', 'nexora-trade' ),
        'footer_explore' => __( 'Footer: Explore', 'nexora-trade' ),
        'footer_resources' => __( 'Footer: Partner resources', 'nexora-trade' ),
    ) );
}
add_action( 'after_setup_theme', 'nexora_trade_setup' );

function nexora_trade_assets() {
    $webfonts = get_theme_mod( 'nexora_load_webfonts', true ) && 'modern' === get_theme_mod( 'nexora_font_style', 'modern' );
    if ( $webfonts ) { wp_enqueue_style( 'nexora-trade-fonts', 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap', array(), null ); }
    wp_enqueue_style( 'nexora-trade-style', get_stylesheet_uri(), array(), '2.8.0' );
    wp_enqueue_style( 'nexora-trade-demo', get_template_directory_uri() . '/assets/elementor-demo.css', array( 'nexora-trade-style' ), '2.8.0' );
    wp_enqueue_script( 'nexora-trade-navigation', get_template_directory_uri() . '/assets/navigation.js', array(), '2.8.0', true );
    wp_enqueue_script( 'nexora-trade-compat', get_template_directory_uri() . '/assets/compatibility.js', array(), '2.8.0', true );
}
add_action( 'wp_enqueue_scripts', 'nexora_trade_assets' );

/** Resolve a portal URL: explicit option, imported demo page, WooCommerce shop, then slug. */
function nexora_trade_url( $slug ) {
    if ( 'home' === $slug ) { return home_url( '/' ); }
    if ( 'support' === $slug && get_theme_mod( 'nexora_support_url', '' ) ) { return get_theme_mod( 'nexora_support_url' ); }
    $demo = get_option( 'nexora_trade_elementor_demo_v2', array() );
    if ( ! empty( $demo['pages'][ $slug ] ) && 'publish' === get_post_status( $demo['pages'][ $slug ] ) ) { return get_permalink( $demo['pages'][ $slug ] ); }
    if ( 'products' === $slug && function_exists( 'wc_get_page_permalink' ) && (int) get_option( 'woocommerce_shop_page_id' ) > 0 ) { return wc_get_page_permalink( 'shop' ); }
    return home_url( '/' . $slug . '/' );
}

function nexora_trade_primary_fallback() {
    $links = array( 'Home' => 'home', 'Products' => 'products', 'Technical' => 'technical', 'Marketing' => 'marketing', 'Training' => 'training', 'Downloads' => 'downloads', 'News' => 'news', 'Support' => 'support' );
    echo '<ul class="portal-nav">';
    foreach ( $links as $label => $slug ) {
        echo '<li><a href="' . esc_url( nexora_trade_url( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

/** Header/footer helper values used by header.php and footer.php. */
function nexora_trade_account_urls() {
    return array(
        'account' => is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url( home_url( '/' ) ),
        'switch' => is_user_logged_in() ? wp_logout_url( home_url( '/' ) ) : wp_login_url( home_url( '/' ) ),
        'switch_label' => is_user_logged_in() ? get_theme_mod( 'nexora_logout_label', 'Log out' ) : get_theme_mod( 'nexora_login_label', 'Log in' ),
    );
}

function nexora_trade_copyright() {
    $text = get_theme_mod( 'nexora_footer_copyright', '' );
    if ( ! $text ) { $text = '© {year} {sitename}. Made for our trade partners.'; }
    return str_replace( array( '{year}', '{sitename}' ), array( gmdate( 'Y' ), get_bloginfo( 'name' ) ), $text );
}

require_once get_template_directory() . '/inc/compat.php';
require_once get_template_directory() . '/inc/branding.php';
require_once get_template_directory() . '/inc/dark-mode.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/theme-options.php';
require_once get_template_directory() . '/inc/portal-shortcodes.php';
require_once get_template_directory() . '/inc/b2b.php';
require_once get_template_directory() . '/inc/elementor-demo-layouts.php';
require_once get_template_directory() . '/inc/demo-import.php';
