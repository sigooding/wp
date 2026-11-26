<?php
/** One-click Elementor Free demo. Creates new content only and tracks exactly what it creates. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

const NEXORA_DEMO_OPTION = 'nexora_trade_elementor_demo_v2';

function nexora_demo_menu_page() {
    add_theme_page( __( 'Portal Demo Content', 'nexora-trade' ), __( 'Portal Demo Content', 'nexora-trade' ), 'manage_options', 'nexora-demo', 'nexora_demo_render_page' );
}
add_action( 'admin_menu', 'nexora_demo_menu_page' );

function nexora_demo_admin_notice() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! current_user_can( 'manage_options' ) || get_option( NEXORA_DEMO_OPTION ) || ( $screen && 'appearance_page_nexora-demo' === $screen->id ) ) { return; }
    echo '<div class="notice notice-info is-dismissible"><p><strong>Aqara Trade Partner Portal:</strong> Create an editable Elementor Free demo. <a href="' . esc_url( admin_url( 'themes.php?page=nexora-demo' ) ) . '">Open the demo installer</a>.</p></div>';
}
add_action( 'admin_notices', 'nexora_demo_admin_notice' );

function nexora_demo_elementor_ready() {
    return did_action( 'elementor/loaded' ) && class_exists( '\\Elementor\\Plugin' );
}

function nexora_demo_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $demo = get_option( NEXORA_DEMO_OPTION, array() );
    $message = isset( $_GET['nexora_demo'] ) ? sanitize_key( wp_unslash( $_GET['nexora_demo'] ) ) : '';
    $error = get_transient( 'nexora_demo_error_' . get_current_user_id() );
    if ( $error ) { delete_transient( 'nexora_demo_error_' . get_current_user_id() ); }
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $ready = nexora_demo_elementor_ready();
    ?>
    <div class="wrap nexora-demo-admin">
        <h1><?php esc_html_e( 'Elementor Trade Portal Demo', 'nexora-trade' ); ?></h1>
        <?php if ( 'imported' === $message ) : ?><div class="notice notice-success"><p>Elementor demo created. Open a page below and choose <strong>Edit with Elementor</strong>.</p></div><?php endif; ?>
        <?php if ( 'products' === $message ) : ?><div class="notice notice-success"><p>WooCommerce demo products added. Your editable Products page will now show them.</p></div><?php endif; ?>
        <?php if ( 'removed' === $message ) : ?><div class="notice notice-success"><p>The Elementor demo was removed. Earlier demos and other existing pages were not deleted.</p></div><?php endif; ?>
        <?php if ( 'already' === $message ) : ?><div class="notice notice-warning"><p>The Elementor demo already exists. Remove it before creating another.</p></div><?php endif; ?>
        <?php if ( $error ) : ?><div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div><?php endif; ?>
        <?php if ( ! $ready ) : ?><div class="notice notice-warning"><p><strong>Elementor Free is required.</strong> Install and activate the free Elementor plugin before importing. <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ); ?>">Find Elementor</a></p></div><?php endif; ?>
        <?php if ( function_exists( 'nexora_compat_notice_rows' ) ) : ?>
        <div class="nexora-compat" style="max-width:860px;background:#fff;border:1px solid #dcdcde;padding:18px 22px;margin-top:18px">
            <h2 style="margin-top:0;font-size:15px">Your plugins</h2>
            <p style="margin-top:0;color:#50575e">Detected automatically. One page builder per page, please — content built in one is not editable in the other.</p>
            <table class="widefat striped" style="max-width:820px"><tbody>
            <?php foreach ( nexora_compat_notice_rows() as $row ) : ?>
                <tr><td style="width:150px"><strong><?php echo esc_html( $row[0] ); ?></strong></td><td><?php echo esc_html( $row[1] ); ?></td><td style="width:70px;text-align:right"><?php echo $row[2] ? '<span style="color:#00a32a">●</span>' : '<span style="color:#c3c4c7">●</span>'; ?></td></tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
        <?php endif; ?>
        <div style="max-width:860px;background:#fff;border:1px solid #dcdcde;padding:24px;margin-top:22px">
            <h2 style="margin-top:0">A real Elementor starting point</h2>
            <p>The installer builds eight pages using native Elementor Free sections, columns, headings, text, buttons, images, icons and shortcode widgets. Open any page in Elementor and edit the individual elements.</p>
            <ul style="list-style:disc;padding-left:22px;line-height:1.8">
                <li>Home, Products, Technical, Marketing, Training, Downloads, News and Support.</li>
                <li>Sample imagery, three sample PDFs, announcements, and a responsive primary menu.</li>
                <li>Optional WooCommerce example products. The editable Products page uses a shortcode; the WooCommerce archive and single-product template remain controlled by the theme.</li>
                <li>A support form that sends via WordPress email. Configure reliable mail delivery before launch.</li>
            </ul>
            <p><strong>Safe by default:</strong> new, separately named pages are created if page slugs already exist; existing page content is never overwritten. You choose whether to switch your homepage or menu.</p>
            <?php if ( get_option( 'nexora_trade_demo_items' ) ) : ?><p><strong>Earlier demo detected:</strong> this installer creates a separate Elementor demo and will not change or delete your previous demo pages.</p><?php endif; ?>
            <?php if ( empty( $demo ) ) : ?>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'nexora_elementor_demo_import' ); ?><input type="hidden" name="action" value="nexora_elementor_demo_import">
                    <p><label><input type="checkbox" name="set_home" value="1" <?php checked( ! (int) get_option( 'page_on_front' ) ); ?>> Set the new demo Home as the static homepage</label></p>
                    <p><label><input type="checkbox" name="set_menu" value="1" <?php checked( empty( $locations['primary'] ) ); ?>> Assign the new demo menu to the primary navigation</label></p>
                    <p><label><input type="checkbox" name="add_news" value="1" checked> Add three example news posts (they appear in the site’s latest posts)</label></p>
                    <p><label><input type="checkbox" name="add_products" value="1" <?php checked( class_exists( 'WooCommerce' ) ); ?> <?php disabled( ! class_exists( 'WooCommerce' ) ); ?>> Add six sample WooCommerce products (<?php echo class_exists( 'WooCommerce' ) ? 'WooCommerce detected' : 'install WooCommerce first, or add them later'; ?>)</label></p>
                    <p><button class="button button-primary button-hero" type="submit" <?php disabled( ! $ready ); ?>>Create Elementor demo</button></p>
                </form>
            <?php else : ?>
                <h3>Your demo pages</h3>
                <p>Click <strong>Edit with Elementor</strong> to change individual sections and widgets. News is populated from WordPress posts; product and support areas use shortcodes.</p>
                <table class="widefat striped" style="max-width:700px"><thead><tr><th>Page</th><th>Actions</th></tr></thead><tbody>
                <?php foreach ( $demo['pages'] as $slug => $id ) : if ( ! get_post( $id ) ) { continue; } ?><tr><td><?php echo esc_html( ucfirst( $slug ) ); ?></td><td><a href="<?php echo esc_url( add_query_arg( array( 'post' => $id, 'action' => 'elementor' ), admin_url( 'post.php' ) ) ); ?>">Edit with Elementor</a> &nbsp; | &nbsp; <a href="<?php echo esc_url( get_permalink( $id ) ); ?>" target="_blank" rel="noopener">View page</a></td></tr><?php endforeach; ?>
                </tbody></table>
                <p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=nexora_portal_options' ) ); ?>">Open theme options</a> <a class="button" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">Edit announcements</a></p>
                <?php if ( class_exists( 'WooCommerce' ) && empty( $demo['products_added'] ) ) : ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'nexora_elementor_demo_products' ); ?><input type="hidden" name="action" value="nexora_elementor_demo_products"><p><button class="button" type="submit">Add example WooCommerce products</button></p></form><?php endif; ?>
                <hr><p><strong>Remove demo:</strong> this permanently deletes the imported pages, posts, products, files and menu—including edits you made to them. Back up or duplicate anything you want to keep. Previous homepage and menu settings are restored only if still pointing to this demo.</p>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Permanently delete the Elementor demo and its imported content?');"><?php wp_nonce_field( 'nexora_elementor_demo_remove' ); ?><input type="hidden" name="action" value="nexora_elementor_demo_remove"><button class="button" type="submit">Remove Elementor demo</button></form>
            <?php endif; ?>
        </div>
        <p>Replace the sample brand, images and PDFs. The theme does not protect private material: add access control and secure uploaded files before publishing.</p>
    </div>
    <?php
}

function nexora_demo_attach_file( $file, &$demo ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $source = get_template_directory() . '/assets/' . $file;
    $result = array( 'id' => 0, 'url' => get_template_directory_uri() . '/assets/' . rawurlencode( $file ) );
    if ( ! is_readable( $source ) ) { return $result; }
    $tmp = wp_tempnam( $file );
    if ( ! $tmp ) { return $result; }
    if ( ! copy( $source, $tmp ) ) { @unlink( $tmp ); return $result; }
    $id = media_handle_sideload( array( 'name' => 'nexora-demo-' . $file, 'tmp_name' => $tmp ), 0 );
    if ( is_wp_error( $id ) ) { @unlink( $tmp ); return $result; }
    $demo['attachments'][] = (int) $id;
    $result['id'] = (int) $id;
    $result['url'] = wp_get_attachment_url( $id );
    return $result;
}

function nexora_demo_create_products( &$demo ) {
    if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) || ! empty( $demo['products_added'] ) ) { return; }
    $items = array(
        array( 'Hub M3', 'The multi-protocol hub for Aqara devices: Zigbee, Thread, Matter and IR.', 'product-hub.jpg', 'Hubs' ),
        array( 'Smart Wall Switch H2', 'Smart control with a familiar wall-switch feel.', 'product-panel.jpg', 'Switches' ),
        array( 'Motion Sensor P1', 'Reliable 360° motion detection for automations and alerts.', 'product-module.jpg', 'Sensors' ),
        array( 'Door & Window Sensor P2', 'Accurate open/close detection for doors, windows and cabinets.', 'product-module.jpg', 'Sensors' ),
        array( 'Temperature & Humidity Sensor W100', 'Precise climate monitoring with a clear E-ink display.', 'product-panel.jpg', 'Climate' ),
        array( 'Water Leak Sensor T1', 'Instant leak alerts for kitchens, bathrooms and plant rooms.', 'product-module.jpg', 'Sensors' ),
    );
    foreach ( $items as $item ) {
        $term = term_exists( $item[3], 'product_cat' );
        if ( ! $term ) {
            $term = wp_insert_term( $item[3], 'product_cat' );
            if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) ) { $demo['terms'][] = (int) $term['term_id']; }
        }
        $product = new WC_Product_Simple();
        $product->set_name( $item[0] );
        $product->set_status( 'publish' );
        $product->set_short_description( $item[1] );
        $product->set_description( '<p>' . esc_html( $item[1] ) . ' Replace this sample with your own product information and specifications.</p>' );
        if ( ! is_wp_error( $term ) && is_array( $term ) && ! empty( $term['term_id'] ) ) { $product->set_category_ids( array( (int) $term['term_id'] ) ); }
        if ( ! empty( $demo['assets'][ $item[2] ]['id'] ) ) { $product->set_image_id( $demo['assets'][ $item[2] ]['id'] ); }
        $id = $product->save();
        if ( $id ) { $demo['products'][] = (int) $id; }
    }
    $demo['products_added'] = ! empty( $demo['products'] );
}

function nexora_demo_delete_created( $demo ) {
    foreach ( array_merge( array_values( $demo['pages'] ?? array() ), $demo['posts'] ?? array() ) as $id ) { if ( get_post( $id ) ) { wp_delete_post( $id, true ); } }
    foreach ( $demo['products'] ?? array() as $id ) {
        $product = function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : false;
        if ( $product ) { $product->delete( true ); } elseif ( get_post( $id ) ) { wp_delete_post( $id, true ); }
    }
    foreach ( $demo['attachments'] ?? array() as $id ) { if ( 'attachment' === get_post_type( $id ) ) { wp_delete_attachment( $id, true ); } }
    if ( ! empty( $demo['menu'] ) ) { wp_delete_nav_menu( $demo['menu'] ); }
    foreach ( $demo['terms'] ?? array() as $id ) { $term = get_term( $id, 'product_cat' ); if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count ) { wp_delete_term( $id, 'product_cat' ); } }
}

function nexora_demo_fail( $demo, $message ) {
    nexora_demo_delete_created( $demo );
    set_transient( 'nexora_demo_error_' . get_current_user_id(), $message, MINUTE_IN_SECONDS );
    wp_safe_redirect( admin_url( 'themes.php?page=nexora-demo' ) );
    exit;
}

function nexora_elementor_demo_import() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'nexora_elementor_demo_import' );
    if ( get_option( NEXORA_DEMO_OPTION ) ) { wp_safe_redirect( admin_url( 'themes.php?page=nexora-demo&nexora_demo=already' ) ); exit; }
    if ( ! nexora_demo_elementor_ready() ) { nexora_demo_fail( array(), 'Please activate Elementor Free before importing the demo.' ); }
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $demo = array( 'pages' => array(), 'posts' => array(), 'products' => array(), 'attachments' => array(), 'terms' => array(), 'assets' => array(), 'menu' => 0, 'products_added' => false, 'previous' => array( 'show_on_front' => get_option( 'show_on_front' ), 'page_on_front' => get_option( 'page_on_front' ), 'primary_menu' => $locations['primary'] ?? 0 ), 'assigned_home' => false, 'assigned_menu' => false );
    $page_names = array( 'home' => 'Home', 'products' => 'Products', 'technical' => 'Technical', 'marketing' => 'Marketing', 'training' => 'Training', 'downloads' => 'Downloads', 'news' => 'News', 'support' => 'Support' );
    foreach ( $page_names as $slug => $title ) {
        $exists = get_page_by_path( $slug );
        $id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $exists ? $title . ' (Elementor Demo)' : $title, 'post_name' => $exists ? $slug . '-elementor-demo' : $slug, 'post_content' => '' ), true );
        if ( is_wp_error( $id ) || ! $id ) { nexora_demo_fail( $demo, 'Unable to create all demo pages. No existing content was changed.' ); }
        $demo['pages'][ $slug ] = (int) $id;
        update_post_meta( $id, '_wp_page_template', 'template-full-width.php' );
    }
    foreach ( array( 'portal-hero.jpg', 'product-hub.jpg', 'product-panel.jpg', 'product-module.jpg', 'installation-guide.jpg', 'product-catalogue-2026.pdf', 'smart-home-installation-guide.pdf', 'marketing-brochure.pdf' ) as $file ) { $demo['assets'][ $file ] = nexora_demo_attach_file( $file, $demo ); }
    $urls = array();
    foreach ( $demo['pages'] as $slug => $id ) { $urls[$slug] = get_permalink( $id ); }
    foreach ( $demo['pages'] as $slug => $id ) {
        $document = \Elementor\Plugin::$instance->documents->get( $id );
        if ( ! $document ) { nexora_demo_fail( $demo, 'Elementor could not open the new page. Please update Elementor and try again.' ); }
        $document->set_is_built_with_elementor( true );
        $layout = nexora_elementor_demo_layout( $slug, $urls, $demo['assets'] );
        $saved = $document->save( array( 'elements' => $layout ) );
        if ( ! $saved || ! get_post_meta( $id, '_elementor_data', true ) ) { nexora_demo_fail( $demo, 'Elementor could not save a demo page. Please check permissions and try again.' ); }
    }
    if ( ! empty( $_POST['add_news'] ) ) {
        foreach ( array(
            array( 'New smart home products launched', 'Meet the latest additions to our connected home range.', '-1 day' ),
            array( 'Updated installation documentation', 'The latest guides are ready when you need them in the Technical section.', '-14 days' ),
            array( 'Latest firmware available', 'Stay up to date with the latest product information. Contact support for details.', '-30 days' ),
        ) as $item ) {
            $id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $item[0], 'post_excerpt' => $item[1], 'post_content' => '<p>' . esc_html( $item[1] ) . '</p><p>Explore your partner resources for product information, training and support.</p><p><em>This is a sample announcement. Replace it with your own news.</em></p>', 'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( $item[2] ) ) ), true );
            if ( ! is_wp_error( $id ) && $id ) { $demo['posts'][] = (int) $id; }
        }
    }
    $menu_name = 'Aqara Elementor Demo ' . wp_generate_password( 5, false, false );
    $menu = wp_create_nav_menu( $menu_name );
    if ( ! is_wp_error( $menu ) ) {
        $demo['menu'] = (int) $menu;
        foreach ( $page_names as $slug => $title ) { wp_update_nav_menu_item( $menu, 0, array( 'menu-item-object-id' => $demo['pages'][$slug], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-title' => $title, 'menu-item-status' => 'publish' ) ); }
    }
    if ( ! empty( $_POST['add_products'] ) ) { nexora_demo_create_products( $demo ); }
    if ( ! empty( $_POST['set_home'] ) ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $demo['pages']['home'] ); $demo['assigned_home'] = true; }
    if ( ! empty( $_POST['set_menu'] ) && $demo['menu'] ) { $locations['primary'] = $demo['menu']; set_theme_mod( 'nav_menu_locations', $locations ); $demo['assigned_menu'] = true; }
    update_option( NEXORA_DEMO_OPTION, $demo, false );
    if ( isset( \Elementor\Plugin::$instance->files_manager ) ) { \Elementor\Plugin::$instance->files_manager->clear_cache(); }
    flush_rewrite_rules();
    wp_safe_redirect( admin_url( 'themes.php?page=nexora-demo&nexora_demo=imported' ) );
    exit;
}
add_action( 'admin_post_nexora_elementor_demo_import', 'nexora_elementor_demo_import' );

function nexora_elementor_demo_products() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'nexora_elementor_demo_products' );
    $demo = get_option( NEXORA_DEMO_OPTION, array() );
    if ( $demo && class_exists( 'WooCommerce' ) ) { nexora_demo_create_products( $demo ); update_option( NEXORA_DEMO_OPTION, $demo, false ); }
    wp_safe_redirect( admin_url( 'themes.php?page=nexora-demo&nexora_demo=products' ) );
    exit;
}
add_action( 'admin_post_nexora_elementor_demo_products', 'nexora_elementor_demo_products' );

function nexora_elementor_demo_remove() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'nexora_elementor_demo_remove' );
    $demo = get_option( NEXORA_DEMO_OPTION, array() );
    if ( ! empty( $demo['assigned_home'] ) && (int) get_option( 'page_on_front' ) === (int) $demo['pages']['home'] && 'page' === get_option( 'show_on_front' ) ) {
        update_option( 'show_on_front', $demo['previous']['show_on_front'] );
        update_option( 'page_on_front', (int) $demo['previous']['page_on_front'] );
    }
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    if ( ! empty( $demo['assigned_menu'] ) && (int) ( $locations['primary'] ?? 0 ) === (int) $demo['menu'] ) { $locations['primary'] = (int) $demo['previous']['primary_menu']; set_theme_mod( 'nav_menu_locations', $locations ); }
    nexora_demo_delete_created( $demo );
    delete_option( NEXORA_DEMO_OPTION );
    if ( nexora_demo_elementor_ready() && isset( \Elementor\Plugin::$instance->files_manager ) ) { \Elementor\Plugin::$instance->files_manager->clear_cache(); }
    wp_safe_redirect( admin_url( 'themes.php?page=nexora-demo&nexora_demo=removed' ) );
    exit;
}
add_action( 'admin_post_nexora_elementor_demo_remove', 'nexora_elementor_demo_remove' );
