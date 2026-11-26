<?php
/**
 * Compatibility helpers for common licensed plugins:
 * Beaver Builder, Max Mega Menu and Smart Slider 3.
 * Elementor remains the demo's native builder; these helpers avoid double
 * navigation controls and surface clear guidance instead of guessing.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nexora_plugin_active( $slug ) {
    switch ( $slug ) {
        case 'elementor': return did_action( 'elementor/loaded' ) || class_exists( '\\Elementor\\Plugin' );
        case 'beaver': return class_exists( 'FLBuilder' ) || defined( 'FL_BUILDER_VERSION' );
        case 'megamenu': return class_exists( 'Mega_Menu' ) || defined( 'MEGAMENU_VERSION' );
        case 'smartslider': return class_exists( 'SmartSlider3' ) || defined( 'NEXTEND_SMARTSLIDER_3_URL_PATH' ) || class_exists( 'N2SS3' );
        case 'apr': return defined( 'APR_VERSION' );
    }
    return false;
}

/** Max Mega Menu replaces the primary menu output and brings its own mobile toggle. */
function nexora_megamenu_controls_navigation() {
    if ( ! nexora_plugin_active( 'megamenu' ) ) { return false; }
    // Max Mega Menu has stored settings both keyed by location and under
    // a "locations" bucket across versions; accept either shape.
    $settings = get_option( 'megamenu_settings', array() );
    if ( ! is_array( $settings ) ) { return false; }
    foreach ( array( $settings['locations']['primary'] ?? null, $settings['primary'] ?? null ) as $entry ) {
        if ( ! is_array( $entry ) ) { continue; }
        if ( array_key_exists( 'enabled', $entry ) ) {
            if ( ! empty( $entry['enabled'] ) ) { return true; }
            continue;
        }
        return true;
    }
    return false;
}

/** Builders used on this site. Content built in one is not editable in the other. */
function nexora_detected_builders() {
    $builders = array();
    if ( nexora_plugin_active( 'elementor' ) ) { $builders[] = 'Elementor'; }
    if ( nexora_plugin_active( 'beaver' ) ) { $builders[] = 'Beaver Builder'; }
    return $builders;
}

function nexora_builder_admin_notice() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $builders = nexora_detected_builders();
    if ( count( $builders ) < 2 ) { return; }
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( $screen && ! in_array( $screen->id, array( 'dashboard', 'themes', 'appearance_page_nexora-demo' ), true ) ) { return; }
    echo '<div class="notice notice-warning"><p><strong>Aqara Trade Partner Portal:</strong> ' . esc_html( implode( ' and ', $builders ) ) . ' are both active. Each page should be built with <em>one</em> builder — content made in one is not editable in the other. The included demo layouts are Elementor-native; if you prefer Beaver Builder, build your own layouts instead of importing the demo.</p></div>';
}
add_action( 'admin_notices', 'nexora_builder_admin_notice' );

/** Tell administrators which optional plugins were detected, with the recommended setup. */
function nexora_compat_summary() {
    return array(
        'elementor' => nexora_plugin_active( 'elementor' ),
        'beaver' => nexora_plugin_active( 'beaver' ),
        'megamenu' => nexora_plugin_active( 'megamenu' ),
        'smartslider' => nexora_plugin_active( 'smartslider' ),
        'megamenu_nav' => nexora_megamenu_controls_navigation(),
    );
}

/** Render a slider shortcode (Smart Slider 3) in the fallback hero when configured. */
function nexora_hero_slider() {
    $shortcode = trim( (string) get_theme_mod( 'nexora_hero_shortcode', '' ) );
    if ( '' === $shortcode || ! nexora_plugin_active( 'smartslider' ) ) { return ''; }
    return do_shortcode( $shortcode );
}

/** Small read-only status list shown in the demo installer so setup decisions are informed. */
function nexora_compat_notice_rows() {
    $status = nexora_compat_summary();
    $rows = array();
    $rows[] = array( 'Elementor Free', $status['elementor'] ? 'Active — demo layouts can be imported.' : 'Not detected. Install Elementor Free before importing the demo.', $status['elementor'] );
    $rows[] = array( 'Beaver Builder', $status['beaver'] ? 'Active — you can build pages with it, but the included demo layouts are Elementor-native. Use one builder per page.' : 'Not detected.', $status['beaver'] );
    $rows[] = array( 'Max Mega Menu', $status['megamenu_nav'] ? 'Controlling the primary menu — the theme has removed its own mobile toggle to avoid a double menu.' : ( $status['megamenu'] ? 'Detected. Assign it to the Primary location under Appearance > Menus or Mega Menu settings.' : 'Not detected. Recommended for a mega menu without Elementor Pro.' ), $status['megamenu'] );
    $rows[] = array( 'Smart Slider 3', $status['smartslider'] ? 'Active — use its shortcode (e.g. [smartslider3 slider="1"]) in an Elementor Shortcode widget or in Theme Options > Homepage hero slider shortcode.' : 'Not detected. Install it, or keep the static hero image.', $status['smartslider'] );
    $apr = nexora_plugin_active( 'apr' );
    $rows[] = array( 'Aqara Partner Resources', $apr ? 'Active — provides the resource library, partner-only downloads, firmware notes, product spec fields, comparison and enquiry list.' : 'Optional companion plugin. Install aqara-partner-resources.zip for managed resources, firmware and product comparison.', $apr );
    return $rows;
}
