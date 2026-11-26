<?php
/** Admin: shortcode reference, sample content importer (with a real sample PDF), download counts column. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_admin_menu() {
    add_submenu_page( 'edit.php?post_type=apr_resource', 'Partner Resources — Help & Setup', 'Help & Setup', 'manage_options', 'apr-help', 'apr_help_page' );
}
add_action( 'admin_menu', 'apr_admin_menu' );

function apr_help_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $msg     = isset( $_GET['apr_msg'] ) ? sanitize_key( wp_unslash( $_GET['apr_msg'] ) ) : '';
    $seeded  = get_option( 'apr_sample_ids' );
    $uploads = wp_get_upload_dir();
    ?>
    <div class="wrap">
      <h1>Aqara Partner Resources</h1>
      <?php if ( 'seeded' === $msg ) : ?><div class="notice notice-success"><p>Sample resources (with sample PDF), firmware notes and a Compare page were created. Everything is marked “SAMPLE”.</p></div><?php endif; ?>
      <?php if ( 'removed' === $msg ) : ?><div class="notice notice-success"><p>Sample content removed.</p></div><?php endif; ?>

      <h2>How it fits together</h2>
      <ol>
        <li><strong>Resources</strong> (left menu) — upload datasheets, brochures, images, certificates. Set a <em>Resource type</em>, tag the <em>Related product</em> (use the product slug, e.g. <code>hub-m3</code>) and choose an <em>Audience</em>. “Partners only” requires login with the <em>Trade Partner</em> role (Users → Add New → Role: Trade Partner).</li>
        <li><strong>Firmware</strong> — one post per release. Fill version, date, type; put release notes in the editor; tag the related product.</li>
        <li><strong>Products</strong> (WooCommerce) — a new “Partner portal details” box lets you set protocols, works-with, specs and in-the-box. These power the shop filters, comparison and product page details. Leave prices blank.</li>
        <li>Place the shortcodes below on pages with an Elementor <em>Shortcode</em> widget (or any editor).</li>
      </ol>

      <h2>Shortcodes</h2>
      <table class="widefat striped" style="max-width:900px">
        <thead><tr><th>Shortcode</th><th>What it shows</th></tr></thead><tbody>
        <tr><td><code>[apr_resources]</code></td><td>Resource library with type filter chips, search and paging. Options: <code>type="datasheet"</code>, <code>product="hub-m3"</code>, <code>limit="12"</code>, <code>filters="no"</code>.</td></tr>
        <tr><td><code>[apr_firmware]</code></td><td>Release notes, newest first, with product filter chips. Option: <code>product="hub-m3"</code>.</td></tr>
        <tr><td><code>[apr_product_resources]</code></td><td>Downloads tagged to the current product. Auto-added to WooCommerce product pages; use the shortcode elsewhere with <code>product="slug"</code>.</td></tr>
        <tr><td><code>[apr_product_filters]</code></td><td>Protocol / Works-with filter bar. Auto-shown on WooCommerce shop pages and above the theme’s <code>[nexora_products]</code> grid.</td></tr>
        <tr><td><code>[apr_compare]</code></td><td>Side-by-side comparison table. Put it on a page with slug <code>compare</code>; the sticky tray links there.</td></tr>
        <tr><td><code>[apr_enquiry_list]</code></td><td>The visitor’s saved enquiry list with a “Send enquiry” button that pre-fills your Support page.</td></tr>
        </tbody>
      </table>
      <p>Archive pages also exist automatically: <code><?php echo esc_html( home_url( '/resources/' ) ); ?></code> and <code><?php echo esc_html( home_url( '/firmware/' ) ); ?></code>. If they 404, visit Settings → Permalinks and click Save once.</p>

      <h2>Sample content</h2>
      <p>Creates 6 sample resources (with a sample PDF attached), 5 firmware notes and a “Compare” page, and fills in specs/SKUs on matching WooCommerce products. Safe to remove later.</p>
      <?php if ( $seeded ) : ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Remove all sample content created by this plugin?');"><?php wp_nonce_field( 'apr_unseed' ); ?><input type="hidden" name="action" value="apr_unseed"><?php submit_button( 'Remove sample content', 'delete', 'submit', false ); ?></form>
      <?php else : ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'apr_seed' ); ?><input type="hidden" name="action" value="apr_seed"><?php submit_button( 'Create sample content', 'primary', 'submit', false ); ?></form>
      <?php endif; ?>

      <h2>Protecting partner-only files properly</h2>
      <p>The plugin serves partner-only files through a login-checked URL, hides them from search/archives/RSS/REST API, and never prints the direct upload link. To also stop someone guessing the raw upload path, place confidential files in <code>wp-content/uploads/partner-only/</code> and block direct HTTP access in <code>.htaccess</code>:</p>
      <pre style="background:#fff;border:1px solid #ccd0d4;padding:12px;max-width:900px">&lt;IfModule mod_rewrite.c&gt;
RewriteEngine On
RewriteRule ^wp-content/uploads/partner-only/ - [F,L]
&lt;/IfModule&gt;</pre>
      <p>On nginx: <code>location ~* /wp-content/uploads/partner-only/ { deny all; }</code>. Upload base: <code><?php echo esc_html( $uploads['basedir'] ); ?></code>. Test while logged out before publishing anything confidential.</p>
    </div>
    <?php
}

/** Sideload the bundled sample PDF so demo resource downloads serve a real PDF file. */
function apr_sideload_sample_pdf( &$ids ) {
    $src = APR_DIR . 'assets/sample-resource.pdf';
    if ( ! is_readable( $src ) ) { return 0; }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $tmp = wp_tempnam( 'sample-resource.pdf' );
    if ( ! $tmp || ! copy( $src, $tmp ) ) { if ( $tmp ) { @unlink( $tmp ); } return 0; }
    $att_id = media_handle_sideload( array( 'name' => 'aqara-sample-resource.pdf', 'tmp_name' => $tmp ), 0 );
    if ( is_wp_error( $att_id ) ) { @unlink( $tmp ); return 0; }
    $ids[] = (int) $att_id;
    return (int) $att_id;
}

/** Sample content: clearly labelled, tracked, removable. */
function apr_seed() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'apr_seed' );
    if ( get_option( 'apr_sample_ids' ) ) { wp_safe_redirect( admin_url( 'edit.php?post_type=apr_resource&page=apr-help' ) ); exit; }
    $ids = array();
    $sample_pdf_id = apr_sideload_sample_pdf( $ids );
    $type    = fn( $n ) => ( term_exists( $n, 'apr_resource_type' ) ?: wp_insert_term( $n, 'apr_resource_type' ) );
    $pub     = term_exists( 'Public', 'apr_audience' );
    $partner = term_exists( 'Partners only', 'apr_audience' );

    $resources = array(
        array( 'SAMPLE Hub M3 datasheet', 'Datasheet', 'hub-m3', 'Key specifications, dimensions and connectivity (sample).', 'Rev 2, Feb 2026', $pub ),
        array( 'SAMPLE Hub M3 quick start guide', 'Installation guide', 'hub-m3', 'Mounting, power and first pairing (sample).', 'Rev 1, Jan 2026', $pub ),
        array( 'SAMPLE Motion Sensor P1 datasheet', 'Datasheet', 'motion-sensor-p1', 'Detection range, battery and mounting (sample).', 'Rev 3', $pub ),
        array( 'SAMPLE Smart Wall Switch H2 wiring guide', 'Installation guide', 'smart-wall-switch-h2', 'Neutral and no-neutral wiring diagrams (sample). Partners only.', 'Rev 4', $partner ),
        array( 'SAMPLE Aqara range brochure 2026', 'Brochure', '', 'Customer-facing overview of the range (sample).', '2026 edition', $pub ),
        array( 'SAMPLE Product image pack', 'Product image', '', 'High-resolution product photography for approved partners (sample). Partners only.', 'Q1 2026', $partner ),
    );
    foreach ( $resources as $r ) {
        $id = wp_insert_post( array( 'post_type' => 'apr_resource', 'post_status' => 'publish', 'post_title' => $r[0], 'post_excerpt' => $r[3], 'post_content' => '<p>' . esc_html( $r[3] ) . ' Replace this sample with the official document.</p>' ) );
        if ( ! $id || is_wp_error( $id ) ) { continue; }
        $ids[] = (int) $id;
        $t = $type( $r[1] ); if ( ! is_wp_error( $t ) && is_array( $t ) ) { wp_set_post_terms( $id, array( (int) $t['term_id'] ), 'apr_resource_type' ); }
        if ( $r[2] ) { wp_set_post_terms( $id, array( $r[2] ), 'apr_product_tag' ); }
        if ( is_array( $r[5] ) && ! empty( $r[5]['term_id'] ) ) { wp_set_post_terms( $id, array( (int) $r[5]['term_id'] ), 'apr_audience' ); }
        update_post_meta( $id, '_apr_version', $r[4] );
        if ( $sample_pdf_id ) { update_post_meta( $id, '_apr_file_id', $sample_pdf_id ); }
    }

    $fw = array(
        array( 'hub-m3', 'Hub M3', '2.1.0', '2026-02-10', 'Feature', 'SAMPLE Matter and Thread border router improvements', "More stable Thread mesh on busy networks\nFaster Matter commissioning" ),
        array( 'hub-m3', 'Hub M3', '2.0.4', '2026-01-14', 'Fix', 'SAMPLE Automation timing fix', 'Sensor-triggered scenes no longer lag after a reboot' ),
        array( 'motion-sensor-p1', 'Motion Sensor P1', '1.0.9', '2026-01-22', 'Fix', 'SAMPLE Detection sensitivity calibration', 'More consistent detection at the edge of the range' ),
        array( 'smart-wall-switch-h2', 'Smart Wall Switch H2', '1.4.2', '2025-12-03', 'Security', 'SAMPLE Pairing security hardening', "Updated pairing security\nRecommended for all installed units" ),
        array( 'temperature-humidity-sensor-w100', 'Temperature & Humidity Sensor W100', '1.2.0', '2025-11-18', 'Feature', 'SAMPLE E-ink refresh schedule', 'Configurable display refresh interval' ),
    );
    foreach ( $fw as $f ) {
        $id = wp_insert_post( array( 'post_type' => 'apr_firmware', 'post_status' => 'publish', 'post_title' => $f[5], 'post_content' => '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', explode( "\n", $f[6] ) ) ) . '</li></ul>' ) );
        if ( ! $id || is_wp_error( $id ) ) { continue; }
        $ids[] = (int) $id;
        wp_set_post_terms( $id, array( $f[0] ), 'apr_product_tag' );
        $term = get_term_by( 'slug', $f[0], 'apr_product_tag' ); if ( $term ) { wp_update_term( $term->term_id, 'apr_product_tag', array( 'name' => $f[1] ) ); }
        update_post_meta( $id, '_apr_fw_version', $f[2] ); update_post_meta( $id, '_apr_fw_date', $f[3] ); update_post_meta( $id, '_apr_kind', $f[4] );
    }

    if ( ! get_page_by_path( 'compare' ) ) {
        $pid = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Compare', 'post_name' => 'compare', 'post_content' => '[apr_compare]' ) );
        if ( $pid && ! is_wp_error( $pid ) ) { $ids[] = (int) $pid; }
    }

    // Enrich all 6 sample WooCommerce products with portal details & SKUs (only when empty).
    $details = array(
        'hub-m3'                           => array( array( 'Zigbee 3.0', 'Thread', 'Matter', 'Wi-Fi', 'Infrared' ), array( 'Apple Home', 'Google Home', 'Amazon Alexa', 'Matter' ), "Hub M3\nUSB-C cable\nQuick start guide", "Power: USB-C 5V (sample)\nLocal control: Yes (sample)\nPlacement: Indoor (sample)" ),
        'motion-sensor-p1'                 => array( array( 'Zigbee 3.0' ), array( 'Apple Home', 'Google Home', 'Amazon Alexa' ), "Sensor\nMounting base\nBattery", "Detection angle: 170° (sample)\nBattery: CR2450 (sample)" ),
        'smart-wall-switch-h2'             => array( array( 'Zigbee 3.0', 'Thread' ), array( 'Apple Home', 'Google Home', 'Amazon Alexa', 'Matter' ), "Switch\nScrews\nGuide", "Fits: standard back box (sample)\nNeutral: with or without, by model (sample)" ),
        'temperature-humidity-sensor-w100' => array( array( 'Zigbee 3.0' ), array( 'Apple Home', 'Google Home' ), "Sensor\nStand\nBattery", "Display: E-ink (sample)\nAccuracy: ±0.3°C (sample)" ),
        'door-window-sensor-p2'            => array( array( 'Thread', 'Matter' ), array( 'Apple Home', 'Google Home', 'Amazon Alexa', 'Matter' ), "Sensor\nMagnet\nAdhesive pads", "Battery: 2 × AAA (sample)\nMounting: Adhesive or screws (sample)" ),
        'water-leak-sensor-t1'             => array( array( 'Zigbee 3.0' ), array( 'Apple Home', 'Google Home', 'Amazon Alexa' ), "Sensor\nQuick start guide", "Water resistance: IP67 (sample)\nBattery: CR2032 (sample)" ),
    );
    $skus = array( 'hub-m3' => 'HM3-G01', 'motion-sensor-p1' => 'MS-S02', 'smart-wall-switch-h2' => 'WS-H2-EU', 'temperature-humidity-sensor-w100' => 'TH-W100', 'door-window-sensor-p2' => 'DW-S03', 'water-leak-sensor-t1' => 'WL-T1' );
    if ( post_type_exists( 'product' ) ) {
        foreach ( $details as $slug => $d ) {
            $p = get_page_by_path( $slug, OBJECT, 'product' );
            if ( ! $p ) { continue; }
            update_post_meta( $p->ID, '_apr_protocols', $d[0] ); update_post_meta( $p->ID, '_apr_ecosystems', $d[1] ); update_post_meta( $p->ID, '_apr_inbox', $d[2] ); update_post_meta( $p->ID, '_apr_specs', $d[3] );
        }
        foreach ( $skus as $slug => $sku ) {
            $p = get_page_by_path( $slug, OBJECT, 'product' );
            if ( $p && function_exists( 'wc_get_product' ) && ( $wc = wc_get_product( $p->ID ) ) && ! $wc->get_sku() ) { $wc->set_sku( $sku ); $wc->save(); }
        }
    }

    update_option( 'apr_sample_ids', $ids, false );
    flush_rewrite_rules();
    wp_safe_redirect( admin_url( 'edit.php?post_type=apr_resource&page=apr-help&apr_msg=seeded' ) );
    exit;
}
add_action( 'admin_post_apr_seed', 'apr_seed' );

function apr_unseed() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'apr_unseed' );
    foreach ( (array) get_option( 'apr_sample_ids', array() ) as $id ) {
        $post = get_post( $id );
        if ( ! $post ) { continue; }
        if ( 'attachment' === $post->post_type ) { wp_delete_attachment( $id, true ); } else { wp_delete_post( $id, true ); }
    }
    delete_option( 'apr_sample_ids' );
    wp_safe_redirect( admin_url( 'edit.php?post_type=apr_resource&page=apr-help&apr_msg=removed' ) );
    exit;
}
add_action( 'admin_post_apr_unseed', 'apr_unseed' );

/** Download count column so you can see what partners actually use. */
add_filter( 'manage_apr_resource_posts_columns', function ( $c ) { $c['apr_dl'] = 'Downloads'; return $c; } );
add_action( 'manage_apr_resource_posts_custom_column', function ( $col, $id ) { if ( 'apr_dl' === $col ) { echo (int) get_post_meta( $id, '_apr_downloads', true ); } }, 10, 2 );

/** Helpful onboarding notice for admins until at least one resource exists. */
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'dashboard' !== $screen->id ) { return; }
    $counts = wp_count_posts( 'apr_resource' );
    if ( $counts && ! empty( $counts->publish ) ) { return; }
    echo '<div class="notice notice-info is-dismissible"><p><strong>Aqara Partner Resources:</strong> add <code>[apr_resources]</code> to your Downloads/Technical pages and <code>[apr_firmware]</code> to a Firmware page. <a href="' . esc_url( admin_url( 'edit.php?post_type=apr_resource&page=apr-help' ) ) . '">Open Help &amp; Setup</a></p></div>';
} );
