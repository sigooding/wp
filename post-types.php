<?php
/** Resource library post type, taxonomies and the "Trade Partner" role. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_register_post_types() {
    register_post_type( 'apr_resource', array(
        'labels' => array(
            'name' => 'Resources', 'singular_name' => 'Resource', 'add_new_item' => 'Add Resource',
            'edit_item' => 'Edit Resource', 'menu_name' => 'Resources', 'all_items' => 'All Resources',
            'search_items' => 'Search Resources', 'not_found' => 'No resources yet.',
        ),
        'public' => true, 'has_archive' => 'resources', 'rewrite' => array( 'slug' => 'resource' ),
        'menu_icon' => 'dashicons-media-document', 'menu_position' => 21,
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
        'show_in_rest' => true,
    ) );

    register_post_type( 'apr_firmware', array(
        'labels' => array(
            'name' => 'Firmware', 'singular_name' => 'Firmware release', 'add_new_item' => 'Add Firmware Release',
            'edit_item' => 'Edit Firmware Release', 'menu_name' => 'Firmware', 'all_items' => 'All Releases',
        ),
        'public' => true, 'has_archive' => 'firmware', 'rewrite' => array( 'slug' => 'firmware-release' ),
        'menu_icon' => 'dashicons-update', 'menu_position' => 22,
        'supports' => array( 'title', 'editor', 'revisions' ),
        'show_in_rest' => true,
    ) );

    register_taxonomy( 'apr_resource_type', 'apr_resource', array(
        'labels' => array( 'name' => 'Resource types', 'singular_name' => 'Resource type' ),
        'hierarchical' => true, 'show_admin_column' => true, 'show_in_rest' => true, 'rewrite' => array( 'slug' => 'resource-type' ),
    ) );

    register_taxonomy( 'apr_audience', array( 'apr_resource', 'apr_firmware' ), array(
        'labels' => array( 'name' => 'Audience', 'singular_name' => 'Audience' ),
        'hierarchical' => true, 'show_admin_column' => true, 'show_in_rest' => true, 'rewrite' => false,
    ) );

    // Attaches resources and firmware to products. Works with WooCommerce products when installed.
    register_taxonomy( 'apr_product_tag', array( 'apr_resource', 'apr_firmware' ), array(
        'labels' => array( 'name' => 'Related products', 'singular_name' => 'Related product' ),
        'hierarchical' => false, 'show_admin_column' => true, 'show_in_rest' => true, 'rewrite' => false,
    ) );
}
add_action( 'init', 'apr_register_post_types' );

/** Seed default terms once. */
function apr_seed_terms() {
    if ( get_option( 'apr_terms_seeded' ) ) { return; }
    foreach ( array( 'Datasheet', 'Installation guide', 'Brochure', 'Product image', 'Certificate', 'Firmware file', 'Price list placeholder' ) as $t ) {
        if ( 'Price list placeholder' === $t ) { continue; } // deliberately not a resource type: no pricing here
        if ( ! term_exists( $t, 'apr_resource_type' ) ) { wp_insert_term( $t, 'apr_resource_type' ); }
    }
    foreach ( array( 'Public', 'Partners only' ) as $t ) {
        if ( ! term_exists( $t, 'apr_audience' ) ) { wp_insert_term( $t, 'apr_audience' ); }
    }
    update_option( 'apr_terms_seeded', 1 );
}
add_action( 'init', 'apr_seed_terms', 20 );

/** A dedicated "Trade Partner" role so partner-only downloads can be granted without admin rights. */
function apr_register_roles() {
    if ( ! get_role( 'trade_partner' ) ) {
        add_role( 'trade_partner', 'Trade Partner', array( 'read' => true, 'apr_download_partner' => true ) );
    }
    foreach ( array( 'administrator', 'editor' ) as $r ) {
        $role = get_role( $r );
        if ( $role ) { $role->add_cap( 'apr_download_partner' ); }
    }
}
add_action( 'init', 'apr_register_roles', 5 );
