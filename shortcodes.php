<?php
/**
 * Shortcodes you can drop into any page (Elementor Shortcode widget, Beaver Builder, or block editor):
 *   [apr_resources type="datasheet" product="hub-m3" limit="12"]   resource library with filters
 *   [apr_firmware product="hub-m3"]                                  release notes, newest first
 *   [apr_product_resources]                                          auto: downloads for the current product
 *   [apr_product_filters]                                            protocol / works-with filter bar
 *   [apr_compare]                                                    comparison table (reads ?items=slug,slug)
 *   [apr_enquiry_list]                                               the partner's saved enquiry list
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_term_names( $post_id, $taxonomy ) {
    $terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
    return ( ! is_wp_error( $terms ) && is_array( $terms ) ) ? $terms : array();
}

function apr_resource_card( $post ) {
    $id      = $post->ID;
    $types   = apr_term_names( $id, 'apr_resource_type' );
    $locked  = apr_is_partner_only( $id ) && ! apr_user_can_download( $id );
    $url     = $locked ? wp_login_url( get_permalink( $id ) ) : apr_download_url( $id );
    $version = (string) get_post_meta( $id, '_apr_version', true );
    $file_id = (int) get_post_meta( $id, '_apr_file_id', true );
    $path    = $file_id ? (string) get_attached_file( $file_id ) : '';
    $exists  = '' !== $path && file_exists( $path );
    $size    = $exists ? size_format( filesize( $path ) ) : '';
    $ext     = $exists ? strtoupper( pathinfo( $path, PATHINFO_EXTENSION ) ) : ( get_post_meta( $id, '_apr_external_url', true ) ? 'LINK' : '' );
    $dl_attr = ( ! $locked && apr_is_direct_download( $id ) ) ? ' download' : '';
    ob_start(); ?>
    <article class="apr-card<?php echo $locked ? ' is-locked' : ''; ?>">
      <?php if ( has_post_thumbnail( $id ) ) : ?><a class="apr-card-thumb" href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo get_the_post_thumbnail( $id, 'medium' ); ?></a><?php endif; ?>
      <div class="apr-card-body">
        <div class="apr-card-meta"><span><?php echo esc_html( ! empty( $types ) ? $types[0] : 'Resource' ); ?></span><?php if ( $ext ) : ?><span class="apr-ext"><?php echo esc_html( $ext ); ?></span><?php endif; ?><?php if ( $locked ) : ?><span class="apr-lock">Partners only</span><?php endif; ?></div>
        <h3><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a></h3>
        <?php if ( has_excerpt( $id ) ) : ?><p><?php echo esc_html( get_the_excerpt( $id ) ); ?></p><?php endif; ?>
        <div class="apr-card-foot">
          <small><?php echo esc_html( trim( $version . ( $size ? " · $size" : '' ), ' ·' ) ); ?></small>
          <?php if ( $url ) : ?><a class="apr-download" href="<?php echo esc_url( $url ); ?>"<?php echo $dl_attr; ?>><?php echo $locked ? 'Log in to download' : 'Download'; ?> ↓</a><?php endif; ?>
        </div>
      </div>
    </article>
    <?php return ob_get_clean();
}

function apr_resources_shortcode( $atts ) {
    $a = shortcode_atts( array( 'type' => '', 'product' => '', 'limit' => 12, 'filters' => 'yes' ), $atts, 'apr_resources' );
    $req_type = isset( $_GET['rtype'] ) ? sanitize_title( wp_unslash( $_GET['rtype'] ) ) : sanitize_title( $a['type'] );
    $req_q    = isset( $_GET['rq'] ) ? sanitize_text_field( wp_unslash( $_GET['rq'] ) ) : '';
    $paged    = max( 1, (int) get_query_var( 'paged' ), isset( $_GET['rpage'] ) ? (int) $_GET['rpage'] : 1 );
    $is_partner = is_user_logged_in() && current_user_can( 'apr_download_partner' );
    $base_tax = array();
    if ( $a['product'] ) { $base_tax[] = array( 'taxonomy' => 'apr_product_tag', 'field' => 'slug', 'terms' => sanitize_title( $a['product'] ) ); }
    if ( ! $is_partner ) { $base_tax[] = array( 'taxonomy' => 'apr_audience', 'field' => 'slug', 'terms' => 'partners-only', 'operator' => 'NOT IN' ); }

    $tax = $base_tax;
    if ( $req_type ) { $tax[] = array( 'taxonomy' => 'apr_resource_type', 'field' => 'slug', 'terms' => $req_type ); }
    $q = new WP_Query( array( 'post_type' => 'apr_resource', 'post_status' => 'publish', 'posts_per_page' => max( 1, (int) $a['limit'] ), 'paged' => $paged, 's' => $req_q, 'tax_query' => $tax, 'orderby' => 'date', 'order' => 'DESC' ) );

    // Compute visible counts per type so guests never see inflated counts including partner-only items.
    $visible_ids = get_posts( array( 'post_type' => 'apr_resource', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => -1, 'tax_query' => $base_tax ) );
    $types = array();
    if ( ! empty( $visible_ids ) ) {
        $raw_types = get_terms( array( 'taxonomy' => 'apr_resource_type', 'hide_empty' => true, 'object_ids' => $visible_ids ) );
        if ( ! is_wp_error( $raw_types ) && is_array( $raw_types ) ) {
            foreach ( $raw_types as $t ) {
                $c = count( get_posts( array( 'post_type' => 'apr_resource', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => -1, 'post__in' => $visible_ids, 'tax_query' => array( array( 'taxonomy' => 'apr_resource_type', 'field' => 'term_id', 'terms' => $t->term_id ) ) ) ) );
                if ( $c > 0 ) { $types[] = array( 'term' => $t, 'count' => $c ); }
            }
        }
    }
    ob_start(); ?>
    <div class="apr-library">
      <?php if ( 'yes' === $a['filters'] ) : ?>
      <form class="apr-filters" method="get" action="">
        <div class="apr-chips" role="group" aria-label="Resource type">
          <a class="apr-chip<?php echo $req_type ? '' : ' is-on'; ?>" href="<?php echo esc_url( remove_query_arg( array( 'rtype', 'rpage' ) ) ); ?>">All</a>
          <?php foreach ( $types as $entry ) : $t = $entry['term']; ?><a class="apr-chip<?php echo $req_type === $t->slug ? ' is-on' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'rtype' => $t->slug, 'rpage' => false ) ) ); ?>"><?php echo esc_html( $t->name ); ?> <em><?php echo (int) $entry['count']; ?></em></a><?php endforeach; ?>
        </div>
        <label class="apr-search"><span class="screen-reader-text">Search resources</span><input type="search" name="rq" value="<?php echo esc_attr( $req_q ); ?>" placeholder="Search resources…"><?php if ( $req_type ) : ?><input type="hidden" name="rtype" value="<?php echo esc_attr( $req_type ); ?>"><?php endif; ?><button type="submit">Search</button></label>
      </form>
      <?php endif; ?>
      <p class="apr-count" aria-live="polite"><?php echo (int) $q->found_posts; ?> <?php echo 1 === (int) $q->found_posts ? 'resource' : 'resources'; ?><?php echo $req_q ? ' matching “' . esc_html( $req_q ) . '”' : ''; ?></p>
      <?php if ( $q->have_posts() ) : ?>
        <div class="apr-grid"><?php while ( $q->have_posts() ) { $q->the_post(); echo apr_resource_card( get_post() ); } wp_reset_postdata(); ?></div>
        <?php if ( $q->max_num_pages > 1 ) : ?><nav class="apr-pager" aria-label="Resource pages"><?php for ( $i = 1; $i <= $q->max_num_pages; $i++ ) { printf( '<a class="%s" href="%s">%d</a>', $i === $paged ? 'is-on' : '', esc_url( add_query_arg( 'rpage', $i ) ), $i ); } ?></nav><?php endif; ?>
      <?php else : ?>
        <div class="apr-empty"><strong>No resources found.</strong> Try another type or search term.</div>
      <?php endif; ?>
      <?php if ( ! $is_partner ) : ?><p class="apr-note">Some resources are partners only. <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">Log in</a> to see them.</p><?php endif; ?>
    </div>
    <?php return ob_get_clean();
}
add_shortcode( 'apr_resources', 'apr_resources_shortcode' );

function apr_firmware_shortcode( $atts ) {
    $a       = shortcode_atts( array( 'product' => '', 'limit' => 20 ), $atts, 'apr_firmware' );
    $product = isset( $_GET['fw_product'] ) ? sanitize_title( wp_unslash( $_GET['fw_product'] ) ) : sanitize_title( $a['product'] );
    $tax     = $product ? array( array( 'taxonomy' => 'apr_product_tag', 'field' => 'slug', 'terms' => $product ) ) : array();
    // Do NOT require _apr_fw_date to exist (so newly published releases without a date never vanish).
    $posts = get_posts( array( 'post_type' => 'apr_firmware', 'post_status' => 'publish', 'posts_per_page' => max( 1, (int) $a['limit'] ), 'tax_query' => $tax ) );
    usort( $posts, function ( $x, $y ) {
        $dx = (string) get_post_meta( $x->ID, '_apr_fw_date', true ) ?: substr( $x->post_date, 0, 10 );
        $dy = (string) get_post_meta( $y->ID, '_apr_fw_date', true ) ?: substr( $y->post_date, 0, 10 );
        return strcmp( $dy, $dx );
    } );
    $fw_ids = get_posts( array( 'post_type' => 'apr_firmware', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => -1 ) );
    $tags   = ! empty( $fw_ids ) ? get_terms( array( 'taxonomy' => 'apr_product_tag', 'hide_empty' => true, 'object_ids' => $fw_ids ) ) : array();
    ob_start(); ?>
    <div class="apr-firmware">
      <?php if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?><div class="apr-chips" role="group" aria-label="Filter by product"><a class="apr-chip<?php echo $product ? '' : ' is-on'; ?>" href="<?php echo esc_url( remove_query_arg( 'fw_product' ) ); ?>">All products</a><?php foreach ( $tags as $t ) : ?><a class="apr-chip<?php echo $product === $t->slug ? ' is-on' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'fw_product', $t->slug ) ); ?>"><?php echo esc_html( $t->name ); ?></a><?php endforeach; ?></div><?php endif; ?>
      <?php if ( ! empty( $posts ) ) : ?>
      <ol class="apr-releases">
        <?php foreach ( $posts as $post_item ) :
            $id     = $post_item->ID;
            $kind   = get_post_meta( $id, '_apr_kind', true ) ?: 'Feature';
            $ver    = (string) get_post_meta( $id, '_apr_fw_version', true );
            $date   = (string) get_post_meta( $id, '_apr_fw_date', true ) ?: substr( $post_item->post_date, 0, 10 );
            $prods  = apr_term_names( $id, 'apr_product_tag' );
            $label  = ( ! empty( $prods ) ? $prods[0] : get_the_title( $id ) ) . ( $ver ? " v$ver" : '' );
            $locked = apr_is_partner_only( $id ) && ! apr_user_can_download( $id );
            $file   = apr_has_file( $id ) ? apr_download_url( $id ) : '';
            $notes  = apply_filters( 'the_content', $post_item->post_content );
            ?>
        <li class="apr-release">
          <div class="apr-release-head"><span class="apr-kind apr-kind-<?php echo esc_attr( strtolower( $kind ) ); ?>"><?php echo esc_html( $kind ); ?></span><?php if ( $date ) : ?><time datetime="<?php echo esc_attr( $date ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></time><?php endif; ?></div>
          <h3><?php echo esc_html( get_the_title( $id ) ); ?></h3>
          <p class="apr-release-product"><?php echo esc_html( $label ); ?></p>
          <div class="apr-release-notes"><?php echo wp_kses_post( $notes ); ?></div>
          <div class="apr-release-actions">
            <?php if ( $file && ! $locked ) : ?><a href="<?php echo esc_url( $file ); ?>"<?php echo apr_is_direct_download( $id ) ? ' download' : ''; ?>>Download firmware ↓</a>
            <?php elseif ( $file && $locked ) : ?><a href="<?php echo esc_url( wp_login_url( get_permalink( $id ) ) ); ?>">Log in to download</a>
            <?php else : ?><a href="<?php echo esc_url( add_query_arg( array( 'apr_topic' => rawurlencode( 'Firmware & software' ), 'apr_product' => rawurlencode( $label ) ), apr_support_page_url() ) ); ?>">Request firmware file →</a><?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
      <?php else : ?><div class="apr-empty"><strong>No release notes yet.</strong> Request the latest file from partner support.</div><?php endif; ?>
    </div>
    <?php return ob_get_clean();
}
add_shortcode( 'apr_firmware', 'apr_firmware_shortcode' );

/** Downloads for the product being viewed (uses get_posts so global $post/$product inside WooCommerce hooks is untouched). */
function apr_product_resources_shortcode( $atts ) {
    $a    = shortcode_atts( array( 'product' => '', 'title' => 'Downloads for this product' ), $atts, 'apr_product_resources' );
    $slug = $a['product'] ? sanitize_title( $a['product'] ) : ( is_singular( 'product' ) ? get_post_field( 'post_name', get_the_ID() ) : '' );
    if ( ! $slug ) { return ''; }
    $items = get_posts( array( 'post_type' => array( 'apr_resource', 'apr_firmware' ), 'post_status' => 'publish', 'posts_per_page' => 20, 'tax_query' => array( array( 'taxonomy' => 'apr_product_tag', 'field' => 'slug', 'terms' => $slug ) ) ) );
    if ( empty( $items ) ) { return ''; }
    ob_start(); ?>
    <section class="apr-product-docs"><h3><?php echo esc_html( $a['title'] ); ?></h3><ul>
    <?php foreach ( $items as $item ) :
        $id     = $item->ID;
        $locked = apr_is_partner_only( $id ) && ! apr_user_can_download( $id );
        $url    = $locked ? wp_login_url( get_permalink( $id ) ) : ( apr_download_url( $id ) ?: get_permalink( $id ) );
        $types  = apr_term_names( $id, 'apr_resource_type' );
        $sub    = 'apr_firmware' === $item->post_type ? 'Firmware ' . get_post_meta( $id, '_apr_fw_version', true ) : ( ! empty( $types ) ? $types[0] : 'Resource' );
        $dl_attr = ( ! $locked && apr_is_direct_download( $id ) ) ? ' download' : '';
        ?>
      <li><span><strong><?php echo esc_html( get_the_title( $id ) ); ?></strong><small><?php echo esc_html( $sub ); ?><?php echo $locked ? ' · Partners only' : ''; ?></small></span><a href="<?php echo esc_url( $url ); ?>"<?php echo $dl_attr; ?>><?php echo $locked ? 'Log in' : 'Download'; ?> ↓</a></li>
    <?php endforeach; ?>
    </ul></section>
    <?php return ob_get_clean();
}
add_shortcode( 'apr_product_resources', 'apr_product_resources_shortcode' );

function apr_enquiry_list_shortcode() {
    return '<div class="apr-enquiry" data-apr-enquiry><p class="apr-count" data-apr-enquiry-empty>Your enquiry list is empty. Add products from the catalogue, then send them to us in one message.</p><ul data-apr-enquiry-items></ul><p class="apr-enquiry-actions"><a class="button" data-apr-enquiry-send href="' . esc_url( apr_support_page_url() ) . '" hidden>Send enquiry</a> <button type="button" class="button-link" data-apr-enquiry-clear hidden>Clear list</button></p><p class="apr-note">No prices are shown or collected. Your list is saved in this browser only.</p></div>';
}
add_shortcode( 'apr_enquiry_list', 'apr_enquiry_list_shortcode' );
