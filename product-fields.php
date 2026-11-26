<?php
/**
 * Extra fields on WooCommerce products: protocols, "works with", in the box, spec rows.
 * Displayed on the single product page and used by catalogue filters and comparison.
 * Degrades gracefully: without WooCommerce nothing here runs.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_protocols()  { return array( 'Zigbee 3.0', 'Thread', 'Matter', 'Wi-Fi', 'Bluetooth', 'Infrared' ); }
function apr_ecosystems() { return array( 'Apple Home', 'Google Home', 'Amazon Alexa', 'Matter', 'SmartThings', 'Home Assistant' ); }

function apr_get_meta_list( $post_id, $key ) {
    $raw = get_post_meta( $post_id, $key, true );
    return is_array( $raw ) ? array_values( array_filter( $raw, 'strlen' ) ) : array();
}

function apr_product_meta_box() {
    if ( ! post_type_exists( 'product' ) ) { return; }
    add_meta_box( 'apr_product_details', 'Partner portal details', 'apr_product_box', 'product', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'apr_product_meta_box' );

function apr_product_box( $post ) {
    wp_nonce_field( 'apr_product_save', 'apr_product_nonce' );
    $protocols = apr_get_meta_list( $post->ID, '_apr_protocols' );
    $eco       = apr_get_meta_list( $post->ID, '_apr_ecosystems' );
    $inbox     = (string) get_post_meta( $post->ID, '_apr_inbox', true );
    $specs     = (string) get_post_meta( $post->ID, '_apr_specs', true );
    ?>
    <p><strong>Protocols</strong><br><?php foreach ( apr_protocols() as $p ) : ?><label style="margin-right:12px"><input type="checkbox" name="apr_protocols[]" value="<?php echo esc_attr( $p ); ?>" <?php checked( in_array( $p, $protocols, true ) ); ?>> <?php echo esc_html( $p ); ?></label><?php endforeach; ?></p>
    <p><strong>Works with</strong><br><?php foreach ( apr_ecosystems() as $e ) : ?><label style="margin-right:12px"><input type="checkbox" name="apr_ecosystems[]" value="<?php echo esc_attr( $e ); ?>" <?php checked( in_array( $e, $eco, true ) ); ?>> <?php echo esc_html( $e ); ?></label><?php endforeach; ?></p>
    <p><label for="apr_inbox"><strong>In the box</strong> (one item per line)</label><br><textarea id="apr_inbox" name="apr_inbox" rows="3" class="widefat"><?php echo esc_textarea( $inbox ); ?></textarea></p>
    <p><label for="apr_specs"><strong>Specifications</strong> (one per line as <code>Label: Value</code>)</label><br><textarea id="apr_specs" name="apr_specs" rows="6" class="widefat" placeholder="Power: USB-C 5V&#10;Operating temperature: -10°C to 50°C"><?php echo esc_textarea( $specs ); ?></textarea></p>
    <p class="description">Use the standard WooCommerce <em>SKU</em> field in Inventory. Prices are hidden by the theme's catalogue mode; leave them blank.</p>
    <?php
}

function apr_product_save( $post_id ) {
    if ( ! isset( $_POST['apr_product_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apr_product_nonce'] ) ), 'apr_product_save' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
    $protocols = isset( $_POST['apr_protocols'] ) ? array_values( array_intersect( apr_protocols(), array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['apr_protocols'] ) ) ) ) : array();
    $eco       = isset( $_POST['apr_ecosystems'] ) ? array_values( array_intersect( apr_ecosystems(), array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['apr_ecosystems'] ) ) ) ) : array();
    update_post_meta( $post_id, '_apr_protocols', $protocols );
    update_post_meta( $post_id, '_apr_ecosystems', $eco );
    update_post_meta( $post_id, '_apr_inbox', isset( $_POST['apr_inbox'] ) ? sanitize_textarea_field( wp_unslash( $_POST['apr_inbox'] ) ) : '' );
    update_post_meta( $post_id, '_apr_specs', isset( $_POST['apr_specs'] ) ? sanitize_textarea_field( wp_unslash( $_POST['apr_specs'] ) ) : '' );
}
add_action( 'save_post_product', 'apr_product_save' );

function apr_parse_specs( $post_id ) {
    $rows = array();
    foreach ( preg_split( '/\r?\n/', (string) get_post_meta( $post_id, '_apr_specs', true ) ) as $line ) {
        if ( strpos( $line, ':' ) === false ) { continue; }
        list( $k, $v ) = array_map( 'trim', explode( ':', $line, 2 ) );
        if ( '' !== $k ) { $rows[ $k ] = $v; }
    }
    return $rows;
}

/** Render details below the product summary without mutating global $post/$product. */
function apr_product_details_output() {
    $id        = get_the_ID();
    $slug      = (string) get_post_field( 'post_name', $id );
    $protocols = apr_get_meta_list( $id, '_apr_protocols' );
    $eco       = apr_get_meta_list( $id, '_apr_ecosystems' );
    $inbox     = array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string) get_post_meta( $id, '_apr_inbox', true ) ) ) );
    $specs     = apr_parse_specs( $id );
    $wc        = function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : null;
    $sku       = $wc ? (string) $wc->get_sku() : '';

    echo '<div class="apr-product-tools">' . apr_compare_button( $id ) . apr_enquiry_button( $id ) . '<button type="button" class="apr-btn apr-print" onclick="window.print()">Print product sheet</button></div>';
    if ( ! empty( $protocols ) ) {
        echo '<ul class="apr-badges" aria-label="Protocols">' . implode( '', array_map( fn( $p ) => '<li>' . esc_html( $p ) . '</li>', $protocols ) ) . '</ul>';
    }
    if ( ! empty( $specs ) ) {
        echo '<section class="apr-specs"><h3>Specifications</h3><dl>';
        foreach ( $specs as $k => $v ) { echo '<div><dt>' . esc_html( $k ) . '</dt><dd>' . esc_html( $v ) . '</dd></div>'; }
        echo '</dl></section>';
    }
    if ( ! empty( $eco ) || ! empty( $inbox ) ) {
        echo '<div class="apr-two-col">';
        if ( ! empty( $eco ) ) { echo '<section><h3>Works with</h3><ul class="apr-chips-static">' . implode( '', array_map( fn( $e ) => '<li>' . esc_html( $e ) . '</li>', $eco ) ) . '</ul></section>'; }
        if ( ! empty( $inbox ) ) { echo '<section><h3>In the box</h3><ul>' . implode( '', array_map( fn( $i ) => '<li>' . esc_html( $i ) . '</li>', $inbox ) ) . '</ul></section>'; }
        echo '</div>';
    }
    echo do_shortcode( '[apr_product_resources product="' . esc_attr( $slug ) . '"]' );

    $fw_posts = get_posts( array( 'post_type' => 'apr_firmware', 'post_status' => 'publish', 'posts_per_page' => 20, 'tax_query' => array( array( 'taxonomy' => 'apr_product_tag', 'field' => 'slug', 'terms' => $slug ) ) ) );
    if ( ! empty( $fw_posts ) ) {
        usort( $fw_posts, function ( $x, $y ) {
            $dx = (string) get_post_meta( $x->ID, '_apr_fw_date', true ) ?: substr( $x->post_date, 0, 10 );
            $dy = (string) get_post_meta( $y->ID, '_apr_fw_date', true ) ?: substr( $y->post_date, 0, 10 );
            return strcmp( $dy, $dx );
        } );
        $latest = $fw_posts[0];
        $ver    = (string) get_post_meta( $latest->ID, '_apr_fw_version', true );
        $fw_url = add_query_arg( 'fw_product', $slug, get_post_type_archive_link( 'apr_firmware' ) ?: home_url( '/firmware/' ) );
        echo '<p class="apr-fw-line">Latest firmware: <strong>' . esc_html( $ver ? 'v' . $ver : get_the_title( $latest->ID ) ) . '</strong> · <a href="' . esc_url( $fw_url ) . '">Release notes</a></p>';
    }
    if ( '' !== $sku ) { echo '<p class="apr-sku">SKU ' . esc_html( $sku ) . '</p>'; }
}
add_action( 'woocommerce_single_product_summary', 'apr_product_details_output', 35 );

/** Validate query-string filters against allowed vocabularies before building SQL meta_query. */
function apr_active_product_filters() {
    $meta = array();
    $map  = array( 'apr_protocol' => array( '_apr_protocols', apr_protocols() ), 'apr_eco' => array( '_apr_ecosystems', apr_ecosystems() ) );
    foreach ( $map as $param => $cfg ) {
        if ( ! empty( $_GET[ $param ] ) ) {
            $val = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );
            if ( in_array( $val, $cfg[1], true ) ) {
                $meta[] = array( 'key' => $cfg[0], 'value' => '"' . $val . '"', 'compare' => 'LIKE' );
            }
        }
    }
    return $meta;
}

/** Catalogue filters on the WooCommerce shop / category archive pages. */
function apr_filter_shop_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! function_exists( 'is_shop' ) ) { return; }
    if ( ! ( is_shop() || is_product_taxonomy() ) ) { return; }
    $extra = apr_active_product_filters();
    if ( empty( $extra ) ) { return; }
    $meta = (array) $query->get( 'meta_query' );
    $query->set( 'meta_query', array_merge( $meta, $extra ) );
}
add_action( 'pre_get_posts', 'apr_filter_shop_query' );

/** Same filters for the [products] shortcode (used by the theme's Elementor Products page). */
function apr_filter_products_shortcode( $args ) {
    $extra = apr_active_product_filters();
    if ( empty( $extra ) ) { return $args; }
    if ( ! isset( $args['meta_query'] ) || ! is_array( $args['meta_query'] ) ) { $args['meta_query'] = array(); }
    $args['meta_query'] = array_merge( $args['meta_query'], $extra );
    return $args;
}
add_filter( 'woocommerce_shortcode_products_query', 'apr_filter_products_shortcode' );

function apr_product_filters_shortcode() { ob_start(); apr_render_filter_bar(); return ob_get_clean(); }
add_shortcode( 'apr_product_filters', 'apr_product_filters_shortcode' );

function apr_render_filter_bar() {
    $cur_p = isset( $_GET['apr_protocol'] ) ? sanitize_text_field( wp_unslash( $_GET['apr_protocol'] ) ) : '';
    $cur_e = isset( $_GET['apr_eco'] ) ? sanitize_text_field( wp_unslash( $_GET['apr_eco'] ) ) : '';
    echo '<form class="apr-shop-filters" method="get"><label>Protocol <select name="apr_protocol"><option value="">Any</option>';
    foreach ( apr_protocols() as $p ) { printf( '<option %s>%s</option>', selected( $cur_p, $p, false ), esc_html( $p ) ); }
    echo '</select></label><label>Works with <select name="apr_eco"><option value="">Any</option>';
    foreach ( apr_ecosystems() as $e ) { printf( '<option %s>%s</option>', selected( $cur_e, $e, false ), esc_html( $e ) ); }
    echo '</select></label><button type="submit">Filter</button>';
    if ( $cur_p || $cur_e ) { echo ' <a href="' . esc_url( remove_query_arg( array( 'apr_protocol', 'apr_eco' ) ) ) . '">Reset</a>'; }
    echo '</form>';
}

function apr_shop_filter_bar() {
    if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() ) ) { return; }
    apr_render_filter_bar();
}
add_action( 'woocommerce_before_shop_loop', 'apr_shop_filter_bar', 15 );

/** Compare + enquiry buttons on catalogue cards. */
function apr_loop_buttons() {
    echo '<div class="apr-loop-tools">' . apr_compare_button( get_the_ID() ) . apr_enquiry_button( get_the_ID() ) . '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'apr_loop_buttons', 12 );
