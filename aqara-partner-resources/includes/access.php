<?php
/**
 * Partner-only downloads, single-post gating, and REST/search/feed filtering.
 * Files tagged "Partners only" are served via /?apr_download=ID (login + capability checked).
 * For strong protection, also block direct web access to a partner-only uploads folder
 * (see Resources → Help & Setup).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_is_partner_only( $post_id ) {
    return has_term( 'partners-only', 'apr_audience', $post_id );
}

function apr_user_can_download( $post_id ) {
    if ( ! apr_is_partner_only( $post_id ) ) { return true; }
    return is_user_logged_in() && current_user_can( 'apr_download_partner' );
}

/** True when a resource or firmware item actually has an uploaded file or external link. */
function apr_has_file( $post_id ) {
    $file_id  = (int) get_post_meta( $post_id, '_apr_file_id', true );
    $external = (string) get_post_meta( $post_id, '_apr_external_url', true );
    if ( $file_id ) {
        $path = (string) get_attached_file( $file_id );
        if ( $path && file_exists( $path ) ) { return true; }
    }
    return '' !== trim( $external );
}

/** Download URL to show in the UI. Returns '' if no file/URL is attached. */
function apr_download_url( $post_id ) {
    if ( ! apr_has_file( $post_id ) ) { return ''; }
    $file_id  = (int) get_post_meta( $post_id, '_apr_file_id', true );
    $external = (string) get_post_meta( $post_id, '_apr_external_url', true );
    if ( apr_is_partner_only( $post_id ) ) {
        return add_query_arg( array( 'apr_download' => $post_id, 'nonce' => wp_create_nonce( 'apr_download_' . $post_id ) ), home_url( '/' ) );
    }
    if ( $file_id ) {
        $url = wp_get_attachment_url( $file_id );
        if ( $url ) { return $url; }
    }
    return $external;
}

/** True when the download URL points to a local/gated file rather than an external webpage. */
function apr_is_direct_download( $post_id ) {
    $file_id = (int) get_post_meta( $post_id, '_apr_file_id', true );
    if ( $file_id && ( $path = (string) get_attached_file( $file_id ) ) && file_exists( $path ) ) { return true; }
    return false;
}

function apr_handle_download() {
    if ( empty( $_GET['apr_download'] ) ) { return; }
    $post_id = absint( $_GET['apr_download'] );
    $nonce   = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
    $post    = get_post( $post_id );
    if ( ! $post || ! in_array( $post->post_type, array( 'apr_resource', 'apr_firmware' ), true ) || 'publish' !== $post->post_status ) {
        wp_die( 'Resource not found.', 'Not found', array( 'response' => 404 ) );
    }
    if ( ! apr_user_can_download( $post_id ) ) {
        if ( is_user_logged_in() ) {
            wp_die( 'Your account does not have permission to download partner-only resources. Ask your portal administrator to assign the Trade Partner role.', 'Access restricted', array( 'response' => 403 ) );
        }
        wp_safe_redirect( wp_login_url( get_permalink( $post_id ) ) );
        exit;
    }
    if ( ! wp_verify_nonce( $nonce, 'apr_download_' . $post_id ) ) {
        wp_safe_redirect( get_permalink( $post_id ) );
        exit;
    }
    $file_id = (int) get_post_meta( $post_id, '_apr_file_id', true );
    $path    = $file_id ? (string) get_attached_file( $file_id ) : '';
    if ( $path && is_readable( $path ) ) {
        update_post_meta( $post_id, '_apr_downloads', (int) get_post_meta( $post_id, '_apr_downloads', true ) + 1 );
        while ( ob_get_level() > 0 ) { ob_end_clean(); }
        nocache_headers();
        $filename = sanitize_file_name( basename( $path ) ) ?: 'download';
        header( 'Content-Type: ' . ( get_post_mime_type( $file_id ) ?: 'application/octet-stream' ) );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Length: ' . filesize( $path ) );
        readfile( $path );
        exit;
    }
    $external = (string) get_post_meta( $post_id, '_apr_external_url', true );
    if ( $external ) {
        update_post_meta( $post_id, '_apr_downloads', (int) get_post_meta( $post_id, '_apr_downloads', true ) + 1 );
        wp_redirect( esc_url_raw( $external ) );
        exit;
    }
    wp_die( 'File missing.', 'Not found', array( 'response' => 404 ) );
}
add_action( 'template_redirect', 'apr_handle_download', 1 );

/** Gate partner-only single views for guests; append download bar for authorised viewers. */
function apr_gate_content( $content ) {
    if ( ! is_singular( array( 'apr_resource', 'apr_firmware' ) ) || ! in_the_loop() || ! is_main_query() ) { return $content; }
    $id = get_the_ID();
    if ( ! apr_user_can_download( $id ) ) {
        $action = is_user_logged_in()
            ? '<p>Your account does not have the <em>Trade Partner</em> role. <a href="' . esc_url( apr_support_page_url() ) . '">Request partner access</a>.</p>'
            : '<p><a class="button" href="' . esc_url( wp_login_url( get_permalink( $id ) ) ) . '">Log in</a> <a href="' . esc_url( apr_support_page_url() ) . '">Request access</a></p>';
        return '<div class="apr-gate"><p><strong>Partners only.</strong> Log in with a trade partner account to view and download this resource.</p>' . $action . '</div>';
    }
    $url = apr_download_url( $id );
    $ver = 'apr_firmware' === get_post_type( $id ) ? get_post_meta( $id, '_apr_fw_version', true ) : get_post_meta( $id, '_apr_version', true );
    $bar = '';
    if ( $url ) {
        $dl_attr = apr_is_direct_download( $id ) ? ' download' : '';
        $bar = '<p class="apr-single-download"><a class="button button-dark" href="' . esc_url( $url ) . '"' . $dl_attr . '>Download' . ( $ver ? ' (' . esc_html( $ver ) . ')' : '' ) . ' ↓</a></p>';
    }
    return $content . $bar;
}
add_filter( 'the_content', 'apr_gate_content' );

function apr_gate_excerpt( $excerpt, $post = null ) {
    $id = $post instanceof WP_Post ? $post->ID : get_the_ID();
    if ( $id && in_array( get_post_type( $id ), array( 'apr_resource', 'apr_firmware' ), true ) && ! apr_user_can_download( $id ) ) {
        return 'Partners only. Log in with a trade partner account to view this resource.';
    }
    return $excerpt;
}
add_filter( 'get_the_excerpt', 'apr_gate_excerpt', 10, 2 );

/** Keep partner-only items out of archives, search, and RSS feeds for guests / non-partners. */
function apr_hide_partner_only( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) { return; }
    if ( is_user_logged_in() && current_user_can( 'apr_download_partner' ) ) { return; }
    $types = array_filter( (array) $query->get( 'post_type' ) );
    $is_plugin_archive = (bool) array_intersect( $types, array( 'apr_resource', 'apr_firmware' ) );
    if ( $query->is_singular() || ( ! $query->is_search() && ! $query->is_feed() && ! $is_plugin_archive ) ) { return; }
    $tax = (array) $query->get( 'tax_query' );
    $tax[] = array( 'taxonomy' => 'apr_audience', 'field' => 'slug', 'terms' => array( 'partners-only' ), 'operator' => 'NOT IN' );
    $query->set( 'tax_query', $tax );
}
add_action( 'pre_get_posts', 'apr_hide_partner_only' );

/** Also exclude partner-only resources and firmware from the public REST API. */
function apr_hide_partner_only_rest( $args ) {
    if ( is_user_logged_in() && current_user_can( 'apr_download_partner' ) ) { return $args; }
    $tax = isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) ? $args['tax_query'] : array();
    $tax[] = array( 'taxonomy' => 'apr_audience', 'field' => 'slug', 'terms' => array( 'partners-only' ), 'operator' => 'NOT IN' );
    $args['tax_query'] = $tax;
    return $args;
}
add_filter( 'rest_apr_resource_query', 'apr_hide_partner_only_rest' );
add_filter( 'rest_apr_firmware_query', 'apr_hide_partner_only_rest' );

/** Block direct single-item REST fetches (/wp-json/wp/v2/apr_resource/<id>) for non-partners. */
function apr_gate_rest_single( $response, $handler, $request ) {
    if ( is_wp_error( $response ) ) { return $response; }
    $route = (string) $request->get_route();
    if ( preg_match( '#^/wp/v2/(apr_resource|apr_firmware)/(\d+)$#', $route, $m ) ) {
        $id = absint( $m[2] );
        if ( $id && apr_is_partner_only( $id ) && ! apr_user_can_download( $id ) ) {
            return new WP_Error( 'apr_forbidden', 'Partners only.', array( 'status' => 403 ) );
        }
    }
    return $response;
}
add_filter( 'rest_request_before_callbacks', 'apr_gate_rest_single', 10, 3 );

function apr_support_page_url() {
    if ( function_exists( 'nexora_trade_url' ) ) { return nexora_trade_url( 'support' ); }
    $page = get_page_by_path( 'support' );
    return $page ? get_permalink( $page ) : home_url( '/support/' );
}

function apr_products_page_url() {
    if ( function_exists( 'nexora_trade_url' ) ) { return nexora_trade_url( 'products' ); }
    if ( function_exists( 'wc_get_page_permalink' ) && (int) get_option( 'woocommerce_shop_page_id' ) > 0 ) { return wc_get_page_permalink( 'shop' ); }
    $page = get_page_by_path( 'products' );
    return $page ? get_permalink( $page ) : home_url( '/products/' );
}

function apr_compare_page_url() {
    $page = get_page_by_path( 'compare' );
    return $page ? get_permalink( $page ) : home_url( '/compare/' );
}
