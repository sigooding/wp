<?php
/**
 * Distributor features: installer FAQ and firmware release notes.
 * Add [aqara_faq] or [aqara_firmware product="hub-m3"] to any page (Elementor Shortcode widget,
 * Beaver Builder text module, or the Classic/Block editor). All content here is SAMPLE data:
 * replace it with official Aqara material before publishing.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aqara_b2b_faqs() {
    return array(
        array( 'Setup', 'Do Zigbee sensors need a hub?', 'Yes. Zigbee devices connect through a compatible hub such as the Hub M3. Check each product’s compatibility list before planning an installation.' ),
        array( 'Setup', 'Which ecosystems are supported?', 'This varies by product. Matter and Thread products can work with Apple Home, Google Home and Amazon Alexa through a compatible hub. Confirm the exact combination on the product page.' ),
        array( 'Installation', 'Can the H2 switch be fitted without a neutral wire?', 'Some H2 variants are designed for no-neutral installations. Confirm the model and regional variant before installing.' ),
        array( 'Installation', 'Where should motion sensors be mounted?', 'Keep them away from direct heat sources and moving objects such as fans or curtains, at the height in the quick start guide.' ),
        array( 'Firmware', 'How do I update device firmware?', 'Updates are delivered through the Aqara app when a device is online. Check Firmware & updates for release notes, or request a firmware file from support.' ),
        array( 'Warranty & returns', 'How do warranty claims work?', 'Contact partner support with the SKU, serial number and a short description of the fault. We’ll confirm the next steps.' ),
    );
}

function aqara_b2b_firmware() {
    return array(
        array( 'hub-m3', 'Hub M3', '2.1.0', '2026-02-10', 'Feature', 'Matter and Thread border router improvements', array( 'More stable Thread mesh on busy networks', 'Faster Matter commissioning from the Aqara app' ) ),
        array( 'hub-m3', 'Hub M3', '2.0.4', '2026-01-14', 'Fix', 'Automation timing fix', array( 'Sensor-triggered scenes no longer lag after a reboot' ) ),
        array( 'motion-sensor-p1', 'Motion Sensor P1', '1.0.9', '2026-01-22', 'Fix', 'Detection sensitivity calibration', array( 'More consistent detection at the edge of the range' ) ),
        array( 'smart-wall-switch-h2', 'Smart Wall Switch H2', '1.4.2', '2025-12-03', 'Security', 'Pairing security hardening', array( 'Updated pairing security', 'Recommended for all installed units' ) ),
        array( 'temperature-humidity-sensor-w100', 'Temperature & Humidity Sensor W100', '1.2.0', '2025-11-18', 'Feature', 'E-ink refresh schedule', array( 'Configurable display refresh interval' ) ),
    );
}

/** [aqara_faq] : grouped, keyboard-accessible accordion built on <details>. */
function aqara_faq_shortcode() {
    $groups = array();
    foreach ( aqara_b2b_faqs() as $item ) { $groups[ $item[0] ][] = $item; }
    $html = '<div class="aqara-faq">';
    foreach ( $groups as $category => $items ) {
        $html .= '<div class="aqara-faq-group"><h3>' . esc_html( $category ) . '</h3>';
        foreach ( $items as $item ) {
            $html .= '<details class="aqara-faq-item"><summary>' . esc_html( $item[1] ) . '</summary><p>' . esc_html( $item[2] ) . '</p></details>';
        }
        $html .= '</div>';
    }
    return $html . '<p class="aqara-demo-note">Sample answers. Replace with official Aqara guidance.</p></div>';
}
add_shortcode( 'aqara_faq', 'aqara_faq_shortcode' );

/** [aqara_firmware] or [aqara_firmware product="hub-m3"] : uses companion plugin releases when present, otherwise sample list. */
function aqara_firmware_shortcode( $atts ) {
    if ( function_exists( 'apr_firmware_shortcode' ) && post_type_exists( 'apr_firmware' ) ) {
        $counts = wp_count_posts( 'apr_firmware' );
        if ( $counts && ! empty( $counts->publish ) ) { return apr_firmware_shortcode( $atts ); }
    }
    $atts  = shortcode_atts( array( 'product' => '' ), $atts, 'aqara_firmware' );
    $only  = sanitize_title( $atts['product'] );
    $rows  = array_filter( aqara_b2b_firmware(), function ( $r ) use ( $only ) { return '' === $only || $r[0] === $only; } );
    usort( $rows, function ( $a, $b ) { return strcmp( $b[3], $a[3] ); } );
    if ( ! $rows ) { return '<p class="aqara-demo-note">No firmware notes for this product yet. Request a file from partner support.</p>'; }
    $support_url = function_exists( 'nexora_trade_url' ) ? nexora_trade_url( 'support' ) : home_url( '/support/' );
    $html = '<ol class="aqara-firmware">';
    foreach ( $rows as $r ) {
        $label = $r[1] . ' v' . $r[2];
        $request = add_query_arg( array( 'apr_topic' => rawurlencode( 'Firmware & software' ), 'apr_product' => rawurlencode( $label ) ), $support_url );
        $html .= '<li class="aqara-release"><div class="aqara-release-head"><span class="aqara-kind aqara-kind-' . esc_attr( strtolower( $r[4] ) ) . '">' . esc_html( $r[4] ) . '</span><time datetime="' . esc_attr( $r[3] ) . '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $r[3] ) ) ) . '</time></div>';
        $html .= '<h3>' . esc_html( $r[5] ) . '</h3><p class="aqara-release-product">' . esc_html( $label ) . '</p><ul>';
        foreach ( $r[6] as $note ) { $html .= '<li>' . esc_html( $note ) . '</li>'; }
        $html .= '</ul><a href="' . esc_url( $request ) . '">Request firmware file →</a></li>';
    }
    return $html . '</ol>';
}
add_shortcode( 'aqara_firmware', 'aqara_firmware_shortcode' );
