<?php
/**
 * Plugin Name: Aqara Partner Resources
 * Description: Resource library (datasheets, brochures, firmware, images), partner-only downloads, product spec fields, catalogue filters, product comparison and enquiry list for a B2B distributor portal. No pricing or checkout. Companion to the Aqara Trade Partner Portal theme, but works with any theme.
 * Version: 1.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Trade Portal
 * License: GPL-2.0-or-later
 * Text Domain: aqara-partner-resources
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'APR_VERSION', '1.1.0' );
define( 'APR_FILE', __FILE__ );
define( 'APR_DIR', plugin_dir_path( __FILE__ ) );
define( 'APR_URL', plugin_dir_url( __FILE__ ) );

require_once APR_DIR . 'includes/post-types.php';
require_once APR_DIR . 'includes/meta.php';
require_once APR_DIR . 'includes/access.php';
require_once APR_DIR . 'includes/shortcodes.php';
require_once APR_DIR . 'includes/product-fields.php';
require_once APR_DIR . 'includes/compare.php';
require_once APR_DIR . 'includes/admin.php';

/** Flush rewrite rules once so /resources/ and /firmware/ URLs work immediately. */
function apr_activate() {
    apr_register_post_types();
    apr_register_roles();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'apr_activate' );

function apr_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'apr_deactivate' );

/** Declare compatibility with WooCommerce High-Performance Order Storage (HPOS) so WooCommerce shows no warning. */
add_action( 'before_woocommerce_init', function () {
    if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', APR_FILE, true );
    }
} );

function apr_assets() {
    wp_enqueue_style( 'apr-front', APR_URL . 'assets/front.css', array(), APR_VERSION );
    wp_enqueue_script( 'apr-front', APR_URL . 'assets/front.js', array(), APR_VERSION, true );
    wp_localize_script( 'apr-front', 'APR', array(
        'compareUrl' => apr_compare_page_url(),
        'supportUrl' => apr_support_page_url(),
        'max'        => 4,
    ) );
}
add_action( 'wp_enqueue_scripts', 'apr_assets' );
