<?php
/** Product comparison (up to 4) and enquiry list. Selection lives in the browser; the table renders server-side from ?items=. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_compare_button( $post_id ) {
    $slug = (string) get_post_field( 'post_name', $post_id );
    return '<button type="button" class="apr-btn apr-compare-toggle" data-apr-compare="' . esc_attr( $slug ) . '" data-name="' . esc_attr( get_the_title( $post_id ) ) . '" aria-pressed="false">Compare</button>';
}

function apr_enquiry_button( $post_id ) {
    $wc  = function_exists( 'wc_get_product' ) ? wc_get_product( $post_id ) : null;
    $sku = $wc ? (string) $wc->get_sku() : '';
    return '<button type="button" class="apr-btn apr-enquiry-toggle" data-apr-enquiry="' . esc_attr( (string) get_post_field( 'post_name', $post_id ) ) . '" data-name="' . esc_attr( get_the_title( $post_id ) . ( '' !== $sku ? " ($sku)" : '' ) ) . '" aria-pressed="false">Add to enquiry</button>';
}

/** Sticky tray markup, printed once in the footer. JS shows it when items exist. */
function apr_compare_tray() {
    echo '<div class="apr-tray" data-apr-tray hidden><span data-apr-tray-count></span><a class="button" data-apr-tray-link href="' . esc_url( apr_compare_page_url() ) . '" hidden>Compare products</a><a class="button" href="' . esc_url( apr_support_page_url() ) . '" data-apr-tray-enquiry hidden>Send enquiry</a><button type="button" data-apr-tray-clear>Clear</button></div>';
}
add_action( 'wp_footer', 'apr_compare_tray' );

function apr_compare_shortcode() {
    $raw   = isset( $_GET['items'] ) ? sanitize_text_field( wp_unslash( $_GET['items'] ) ) : '';
    $slugs = array_slice( array_unique( array_filter( array_map( 'sanitize_title', explode( ',', $raw ) ) ) ), 0, 4 );
    $browse_url = apr_products_page_url();
    if ( ! $slugs || ! post_type_exists( 'product' ) ) {
        return '<div class="apr-empty"><strong>Nothing to compare yet.</strong> Choose up to 4 products from the catalogue, then select Compare. <a href="' . esc_url( $browse_url ) . '">Browse products</a></div>';
    }
    $posts = array();
    foreach ( $slugs as $s ) {
        $p = get_page_by_path( $s, OBJECT, 'product' );
        if ( $p && 'publish' === $p->post_status ) { $posts[] = $p; }
    }
    if ( ! $posts ) {
        return '<div class="apr-empty">Those products could not be found. <a href="' . esc_url( $browse_url ) . '">Browse products</a></div>';
    }
    $spec_keys = array();
    foreach ( $posts as $p ) { $spec_keys = array_merge( $spec_keys, array_keys( apr_parse_specs( $p->ID ) ) ); }
    $spec_keys = array_values( array_unique( $spec_keys ) );
    $rows = array(
        'SKU'        => function ( $p ) { $wc = function_exists( 'wc_get_product' ) ? wc_get_product( $p->ID ) : null; return $wc ? (string) $wc->get_sku() : ''; },
        'Category'   => fn( $p ) => implode( ', ', apr_term_names( $p->ID, 'product_cat' ) ),
        'Protocols'  => fn( $p ) => implode( ', ', apr_get_meta_list( $p->ID, '_apr_protocols' ) ),
        'Works with' => fn( $p ) => implode( ', ', apr_get_meta_list( $p->ID, '_apr_ecosystems' ) ),
    );
    foreach ( $spec_keys as $k ) { $rows[ $k ] = fn( $p ) => apr_parse_specs( $p->ID )[ $k ] ?? '—'; }
    $rows['In the box'] = fn( $p ) => implode( ', ', array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string) get_post_meta( $p->ID, '_apr_inbox', true ) ) ) ) );
    ob_start(); ?>
    <div class="apr-compare-actions"><a class="button" href="<?php echo esc_url( $browse_url ); ?>">Add more products</a> <button type="button" class="button" onclick="window.print()">Print comparison</button></div>
    <div class="apr-compare-scroll"><table class="apr-compare"><caption class="screen-reader-text">Product comparison</caption><thead><tr><th scope="col">Feature</th>
    <?php foreach ( $posts as $p ) : $rest = array_diff( $slugs, array( $p->post_name ) ); ?><th scope="col"><?php echo get_the_post_thumbnail( $p->ID, 'medium' ); ?><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( $p->post_title ); ?></a><a class="apr-remove" href="<?php echo esc_url( $rest ? add_query_arg( 'items', implode( ',', $rest ) ) : remove_query_arg( 'items' ) ); ?>">Remove</a></th><?php endforeach; ?>
    </tr></thead><tbody>
    <?php foreach ( $rows as $label => $fn ) : ?><tr><th scope="row"><?php echo esc_html( $label ); ?></th><?php foreach ( $posts as $p ) : ?><td><?php echo esc_html( $fn( $p ) ?: '—' ); ?></td><?php endforeach; ?></tr><?php endforeach; ?>
    </tbody></table></div>
    <?php return ob_get_clean();
}
add_shortcode( 'apr_compare', 'apr_compare_shortcode' );

/** Pre-fill any support form on the current page from ?apr_topic / ?apr_product or the saved enquiry list. */
function apr_support_prefill_script() {
    if ( is_admin() ) { return; }
    ?>
    <script>
    (function(){
      var form = document.querySelector('form.nexora-partner-form');
      if (!form) return;
      var q = new URLSearchParams(location.search), topic = q.get('apr_topic'), product = q.get('apr_product');
      var sel = form.querySelector('select[name="topic"]'), msg = form.querySelector('textarea[name="message"]');
      if (sel && topic) { for (var o of sel.options) { if (o.value === topic || o.text === topic) { sel.value = o.value; break; } } }
      var list = []; try { list = JSON.parse(localStorage.getItem('apr-enquiry') || '[]'); } catch (e) {}
      if (msg && !msg.value.trim()) {
        if (product) { msg.value = 'Hi, I\u2019d like more information about ' + product + '.\n\n'; }
        else if (list.length) { msg.value = 'Please send availability and product information for:\n- ' + list.map(function(i){ return i.name; }).join('\n- ') + '\n\n'; }
      }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'apr_support_prefill_script' );
