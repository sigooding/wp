<?php
/** Behaviour driven by Trade Portal Options: catalogue mode, private areas, login branding, notices. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nexora_catalogue_mode() { return (bool) get_theme_mod( 'nexora_catalogue_mode', true ); }

/* ---- WooCommerce: catalogue-only product details ---- */
add_filter( 'woocommerce_is_purchasable', static function ( $value ) { return nexora_catalogue_mode() ? false : $value; }, 999 );
add_filter( 'woocommerce_variation_is_purchasable', static function ( $value ) { return nexora_catalogue_mode() ? false : $value; }, 999 );
add_filter( 'woocommerce_get_price_html', static function ( $value ) { return nexora_catalogue_mode() ? '' : $value; }, 999 );
add_filter( 'woocommerce_loop_add_to_cart_link', static function ( $value ) { return nexora_catalogue_mode() ? '' : $value; }, 999 );
add_filter( 'woocommerce_add_to_cart_validation', static function ( $value ) { return nexora_catalogue_mode() ? false : $value; }, 999 );
add_filter( 'loop_shop_columns', static function () {
    return (int) nexora_sanitize_columns( (string) get_theme_mod( 'nexora_product_columns', '4' ) );
} );

function nexora_trade_catalogue_actions() {
    if ( nexora_catalogue_mode() ) {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    }
    if ( get_theme_mod( 'nexora_hide_related_products', true ) ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    }
    if ( ! get_theme_mod( 'nexora_show_product_meta', false ) ) {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    }
}
add_action( 'wp_loaded', 'nexora_trade_catalogue_actions' );

function nexora_trade_product_inquiry() {
    if ( ! get_theme_mod( 'nexora_show_product_inquiry', true ) ) { return; }
    $url = get_theme_mod( 'nexora_product_inquiry_url', '' );
    if ( ! $url ) { $url = nexora_trade_url( 'support' ); }
    // Carry the product into the support form. Uses apr_* names: WooCommerce reserves ?product= for its own routing.
    $product_label = get_the_title();
    if ( function_exists( 'wc_get_product' ) ) {
        $wc = wc_get_product( get_the_ID() );
        if ( $wc && $wc->get_sku() ) { $product_label .= ' (' . $wc->get_sku() . ')'; }
    }
    $url = add_query_arg( array( 'apr_topic' => rawurlencode( 'Product information' ), 'apr_product' => rawurlencode( $product_label ) ), $url );
    $button_label = get_theme_mod( 'nexora_product_inquiry_label', 'Ask about this product' );
    echo '<p class="nexora-product-inquiry"><a class="button button-dark" href="' . esc_url( $url ) . '">' . esc_html( $button_label ) . ' →</a></p>';
}
add_action( 'woocommerce_single_product_summary', 'nexora_trade_product_inquiry', 31 );

function nexora_trade_commerce_page_gate() {
    if ( ! nexora_catalogue_mode() || ! function_exists( 'is_cart' ) ) { return; }
    if ( is_cart() || is_checkout() || is_account_page() ) {
        wp_safe_redirect( home_url( '/' ) );
        exit;
    }
}
add_action( 'template_redirect', 'nexora_trade_commerce_page_gate' );

/** Warn admins if WooCommerce Coming Soon mode is hiding product pages from visitors. */
function nexora_trade_woo_coming_soon_notice() {
    if ( ! current_user_can( 'manage_options' ) || ! class_exists( 'WooCommerce' ) ) { return; }
    if ( 'yes' !== get_option( 'woocommerce_coming_soon', 'no' ) ) { return; }
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( $screen && ! in_array( $screen->id, array( 'dashboard', 'themes', 'appearance_page_nexora-demo', 'edit-product' ), true ) ) { return; }
    echo '<div class="notice notice-warning is-dismissible"><p><strong>WooCommerce Coming Soon mode is active:</strong> product pages are hidden from logged-out visitors. Go to <a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=site-visibility' ) ) . '">WooCommerce → Settings → Site visibility</a> and choose <strong>Live</strong> when you are ready.</p></div>';
}
add_action( 'admin_notices', 'nexora_trade_woo_coming_soon_notice' );

/* ---- News & posts ---- */
add_filter( 'pre_option_posts_per_page', static function ( $value ) {
    if ( is_admin() ) { return $value; } // don't change admin list-table paging
    $per_page = absint( get_theme_mod( 'nexora_posts_per_page', 10 ) );
    return $per_page ? $per_page : $value;
} );

/** One-click editing shortcuts in the admin bar for anyone who can edit pages. */
function nexora_trade_admin_bar( $wp_admin_bar ) {
    if ( ! current_user_can( 'edit_pages' ) ) { return; }
    $options_url = admin_url( 'customize.php?autofocus[panel]=nexora_portal_options' );
    $wp_admin_bar->add_node( array( 'id' => 'nexora-portal', 'title' => __( 'Trade Portal', 'nexora-trade' ), 'href' => $options_url ) );
    $wp_admin_bar->add_node( array( 'parent' => 'nexora-portal', 'id' => 'nexora-portal-options', 'title' => __( 'Theme options', 'nexora-trade' ), 'href' => $options_url ) );
    $wp_admin_bar->add_node( array( 'parent' => 'nexora-portal', 'id' => 'nexora-portal-demo', 'title' => __( 'Demo content', 'nexora-trade' ), 'href' => admin_url( 'themes.php?page=nexora-demo' ) ) );
    $wp_admin_bar->add_node( array( 'parent' => 'nexora-portal', 'id' => 'nexora-portal-menus', 'title' => __( 'Menus', 'nexora-trade' ), 'href' => admin_url( 'nav-menus.php' ) ) );
    $front = (int) get_option( 'page_on_front' );
    if ( $front && nexora_plugin_active( 'elementor' ) ) {
        $wp_admin_bar->add_node( array( 'parent' => 'nexora-portal', 'id' => 'nexora-portal-home', 'title' => __( 'Edit homepage (Elementor)', 'nexora-trade' ), 'href' => admin_url( 'post.php?post=' . $front . '&action=elementor' ) ) );
    }
}
add_action( 'admin_bar_menu', 'nexora_trade_admin_bar', 80 );

/* ---- Private areas (basic login gate; use a membership plugin for real access control) ---- */
function nexora_trade_private_area_gate() {
    if ( is_admin() || is_user_logged_in() ) { return; }
    $raw = (string) get_theme_mod( 'nexora_private_slugs', '' );
    if ( '' === trim( $raw ) ) { return; }
    $slugs = array_filter( array_map( 'trim', explode( ',', strtolower( $raw ) ) ) );
    $current = '';
    if ( is_singular() ) {
        $current = (string) get_post_field( 'post_name', get_queried_object_id() );
    } elseif ( is_post_type_archive() ) {
        $pt = get_query_var( 'post_type' );
        $current = is_array( $pt ) ? (string) reset( $pt ) : (string) $pt;
    }
    if ( '' === $current || ! in_array( $current, $slugs, true ) ) { return; }
    $requested = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
    wp_safe_redirect( wp_login_url( home_url( $requested ) ) );
    exit;
}
add_action( 'template_redirect', 'nexora_trade_private_area_gate' );

/* ---- Branded login screen ---- */
function nexora_trade_login_branding() {
    if ( ! get_theme_mod( 'nexora_branded_login', false ) ) { return; }
    $logo = get_theme_mod( 'nexora_login_logo', '' );
    $background = nexora_colour( 'nexora_login_background', '#f5f8f5' );
    $accent = nexora_colour( 'nexora_accent_color', '#0E6B57' );
    $system = 'system' === nexora_sanitize_font( get_theme_mod( 'nexora_font_style', 'modern' ) ) || ! get_theme_mod( 'nexora_load_webfonts', true );
    $heading = $system ? "system-ui, -apple-system, 'Segoe UI', sans-serif" : 'Manrope, sans-serif';
    $css = 'body.login{background:' . $background . ';font-family:' . ( $system ? "system-ui, -apple-system, 'Segoe UI', sans-serif" : "'DM Sans', Arial, sans-serif" ) . '}';
    $css .= '.login h1 a{background-image:none;width:auto;height:auto;text-indent:0;font:700 22px ' . $heading . ';color:' . $accent . '}';
    if ( $logo ) { $css .= '.login h1 a{background-image:url(' . esc_url( $logo ) . ');background-size:contain;background-repeat:no-repeat;background-position:center;height:64px;width:100%}'; }
    $css .= '.login #nav a,.login #backtoblog a{color:' . $accent . '}';
    $css .= '.login .button-primary,.login input[type=submit]{background:' . $accent . ';border-color:' . $accent . ';height:40px}';
    $css .= '.login form{border-radius:9px;border:1px solid rgba(30,60,45,.12);box-shadow:0 10px 30px rgba(20,45,32,.08)}';
    $css .= '.nexora-login-heading{font:700 13px ' . $heading . ';color:' . $accent . ';text-transform:uppercase;letter-spacing:.08em;text-align:center;margin:0 0 6px}';
    wp_add_inline_style( 'login', $css );
}
add_action( 'login_enqueue_scripts', 'nexora_trade_login_branding' );

add_filter( 'login_headertext', static function ( $text ) { return get_theme_mod( 'nexora_branded_login', false ) ? get_bloginfo( 'name' ) : $text; } );
add_filter( 'login_headerurl', static function ( $url ) { return get_theme_mod( 'nexora_branded_login', false ) ? home_url( '/' ) : $url; } );
add_filter( 'login_message', static function ( $message ) {
    if ( ! get_theme_mod( 'nexora_branded_login', false ) ) { return $message; }
    $heading = get_theme_mod( 'nexora_login_heading', '' );
    return $heading ? '<p class="nexora-login-heading">' . esc_html( $heading ) . '</p>' . $message : $message;
} );
add_filter( 'login_redirect', static function ( $redirect_to, $requested_redirect_to, $user ) {
    if ( ! ( $user instanceof WP_User ) || user_can( $user, 'manage_options' ) ) { return $redirect_to; }
    $target = get_theme_mod( 'nexora_login_redirect', '' );
    if ( $target ) { return $target; }
    return $requested_redirect_to ? $requested_redirect_to : $redirect_to;
}, 10, 3 );

/* ---- Sample-content reminder for administrators only ---- */
function nexora_trade_sample_notice() {
    if ( ! get_theme_mod( 'nexora_show_sample_notice', true ) || ! current_user_can( 'manage_options' ) ) { return; }
    if ( ! get_option( 'nexora_trade_elementor_demo_v2' ) && ! get_option( 'nexora_trade_demo_items' ) ) { return; }
    echo '<div class="nexora-sample-notice"><span>Sample portal content is installed</span> Replace the demo text, images and PDFs before publishing. <a href="' . esc_url( admin_url( 'themes.php?page=nexora-demo' ) ) . '">Manage demo</a> <a href="' . esc_url( admin_url( 'customize.php?autofocus[panel]=nexora_portal_options' ) ) . '">Theme options</a></div>';
}
add_action( 'wp_body_open', 'nexora_trade_sample_notice' );
