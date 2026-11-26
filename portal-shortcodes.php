<?php
/** Dynamic elements that work inside Elementor Free's Shortcode widget. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nexora_latest_news_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'limit' => 3 ), $atts, 'nexora_latest_news' );
    $limit = max( 1, min( 20, absint( $atts['limit'] ) ) );
    $posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $limit, 'ignore_sticky_posts' => true ) );
    if ( ! $posts ) { return '<p class="nexora-shortcode-empty">News and announcements will appear here when you publish a post.</p>'; }
    $html = '<div class="nexora-news-list">';
    foreach ( $posts as $post_item ) {
        $excerpt = $post_item->post_excerpt ? $post_item->post_excerpt : $post_item->post_content;
        $html .= '<a class="nexora-news-item" href="' . esc_url( get_permalink( $post_item ) ) . '"><span class="nexora-news-date"><strong>' . esc_html( get_the_date( 'd', $post_item ) ) . '</strong><small>' . esc_html( strtoupper( get_the_date( 'M', $post_item ) ) ) . '</small></span><span class="nexora-news-copy"><small>PARTNER NEWS</small><strong>' . esc_html( get_the_title( $post_item ) ) . '</strong><span>' . esc_html( wp_trim_words( wp_strip_all_tags( $excerpt ), 14 ) ) . '</span></span><span class="nexora-news-arrow" aria-hidden="true">↗</span></a>';
    }
    return $html . '</div>';
}
add_shortcode( 'nexora_latest_news', 'nexora_latest_news_shortcode' );

function nexora_products_shortcode() {
    if ( function_exists( 'wc_get_products' ) && shortcode_exists( 'products' ) ) {
        $products = wc_get_products( array( 'limit' => 1, 'status' => 'publish', 'return' => 'ids' ) );
        if ( $products ) {
            $cols = function_exists( 'nexora_sanitize_columns' ) ? nexora_sanitize_columns( (string) get_theme_mod( 'nexora_product_columns', '4' ) ) : '4';
            $filters = shortcode_exists( 'apr_product_filters' ) ? do_shortcode( '[apr_product_filters]' ) : '';
            return '<div class="nexora-woo-catalogue">' . $filters . do_shortcode( '[products limit="12" columns="' . esc_attr( $cols ) . '" paginate="true"]' ) . '</div>';
        }
    }
    $items = array(
        array( 'Hub M3', 'HUBS', 'The multi-protocol hub for Aqara devices.', 'product-hub.jpg' ),
        array( 'Smart Wall Switch H2', 'SWITCHES', 'Smart control with a familiar wall-switch feel.', 'product-panel.jpg' ),
        array( 'Motion Sensor P1', 'SENSORS', 'Reliable 360° motion detection.', 'product-module.jpg' ),
        array( 'Door & Window Sensor P2', 'SENSORS', 'Accurate open/close detection.', 'product-module.jpg' ),
    );
    $html = '<div class="nexora-sample-products">';
    foreach ( $items as $item ) {
        $html .= '<article class="nexora-sample-product"><img src="' . esc_url( get_template_directory_uri() . '/assets/' . $item[3] ) . '" alt=""><div><small>' . esc_html( strtoupper( $item[1] ) ) . '</small><h3>' . esc_html( $item[0] ) . '</h3><p>' . esc_html( $item[2] ) . '</p><a href="' . esc_url( nexora_trade_url( 'support' ) ) . '">Ask about this product ↗</a></div></article>';
    }
    return $html . '</div><p class="nexora-demo-disclaimer">These are example products. Activate WooCommerce and add products to display your own catalogue here.</p>';
}
add_shortcode( 'nexora_products', 'nexora_products_shortcode' );

function nexora_partner_support_shortcode() {
    $status = isset( $_GET['nexora_message'] ) ? sanitize_key( wp_unslash( $_GET['nexora_message'] ) ) : '';
    $topics = array( 'Product information', 'Technical support', 'Firmware & software', 'Warranty & returns', 'Marketing resources', 'Training', 'Portal access', 'Other' );
    $pre_topic   = isset( $_GET['apr_topic'] ) ? sanitize_text_field( wp_unslash( $_GET['apr_topic'] ) ) : '';
    $pre_topic   = in_array( $pre_topic, $topics, true ) ? $pre_topic : '';
    $pre_product = isset( $_GET['apr_product'] ) ? sanitize_text_field( wp_unslash( $_GET['apr_product'] ) ) : '';
    $pre_message = $pre_product ? 'Hi, I\'d like more information about ' . $pre_product . ".\n\n" : '';
    $messages = array(
        'sent' => 'Your message has been sent for delivery. Thank you for getting in touch.',
        'invalid' => 'Please complete all fields with a valid email address and try again.',
        'error' => 'The message could not be sent. Please contact the site administrator or try again later.',
        'slow' => 'Please wait a moment before sending another message.',
    );
    ob_start();
    if ( isset( $messages[ $status ] ) ) { echo '<p role="status" class="nexora-form-message ' . ( 'sent' === $status ? 'is-success' : 'is-error' ) . '">' . esc_html( $messages[ $status ] ) . '</p>'; }
    ?>
    <form class="nexora-partner-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
        <input type="hidden" name="action" value="nexora_partner_support">
        <?php wp_nonce_field( 'nexora_partner_support', 'nexora_nonce' ); ?>
        <p class="nexora-honeypot" aria-hidden="true"><label>Leave this field empty <input name="website" tabindex="-1" autocomplete="off"></label></p>
        <div class="nexora-form-grid"><label>Your name<input name="name" required maxlength="120" autocomplete="name"></label><label>Email address<input type="email" name="email" required maxlength="254" autocomplete="email"></label></div>
        <label>How can we help?<select name="topic" required><option value="">Select a topic</option><?php foreach ( $topics as $t ) { printf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $t ), selected( $pre_topic, $t, false ) ); } ?></select></label>
        <label>Message<textarea name="message" required maxlength="5000" rows="6" placeholder="Tell us a little more about your question..."><?php echo esc_textarea( $pre_message ); ?></textarea></label>
        <button type="submit">Send message →</button><small>Your details are used to respond to your request.</small>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'nexora_partner_support', 'nexora_partner_support_shortcode' );

function nexora_partner_support_submit() {
    $referer = wp_get_referer();
    $back = $referer ? remove_query_arg( array( 'nexora_message', 'apr_topic', 'apr_product' ), $referer ) : nexora_trade_url( 'support' );
    $status = 'invalid';
    $nonce = isset( $_POST['nexora_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nexora_nonce'] ) ) : '';
    $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $topic = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
    $website = isset( $_POST['website'] ) ? trim( wp_unslash( $_POST['website'] ) ) : '';
    $topics = array( 'Product information', 'Technical support', 'Firmware & software', 'Warranty & returns', 'Marketing resources', 'Training', 'Portal access', 'Other' );
    if ( $website ) { $status = 'sent'; }
    elseif ( wp_verify_nonce( $nonce, 'nexora_partner_support' ) && $name && strlen( $name ) <= 120 && is_email( $email ) && strlen( $email ) <= 254 && in_array( $topic, $topics, true ) && $message && strlen( $message ) <= 5000 ) {
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
        $key = 'nexora_s_' . substr( wp_hash( $ip ), 0, 20 );
        if ( get_transient( $key ) ) { $status = 'slow'; }
        else {
            set_transient( $key, 1, 30 );
            $recipient = sanitize_email( get_theme_mod( 'nexora_contact_email', get_option( 'admin_email' ) ) );
            if ( ! is_email( $recipient ) ) { $recipient = get_option( 'admin_email' ); }
            $body = "Partner support request\n\nName: " . $name . "\nEmail: " . $email . "\nTopic: " . $topic . "\n\n" . $message;
            $status = wp_mail( $recipient, '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] Partner support: ' . $topic, $body, array( 'Reply-To: ' . $email ) ) ? 'sent' : 'error';
        }
    }
    wp_safe_redirect( add_query_arg( 'nexora_message', $status, $back ) );
    exit;
}
add_action( 'admin_post_nexora_partner_support', 'nexora_partner_support_submit' );
add_action( 'admin_post_nopriv_nexora_partner_support', 'nexora_partner_support_submit' );
