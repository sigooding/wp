<?php
/** Theme-wide options (WordPress Customizer). Page content stays editable in Elementor. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nexora_sanitize_checkbox( $value ) { return (bool) $value; }
function nexora_sanitize_choice( $value, $allowed, $fallback ) { return in_array( $value, $allowed, true ) ? $value : $fallback; }
function nexora_sanitize_width( $value ) { return nexora_sanitize_choice( $value, array( '1100', '1320', '1440' ), '1320' ); }
function nexora_sanitize_radius( $value ) { return nexora_sanitize_choice( $value, array( '4', '9', '16' ), '9' ); }
function nexora_sanitize_font( $value ) { return nexora_sanitize_choice( $value, array( 'modern', 'system' ), 'modern' ); }
function nexora_sanitize_font_size( $value ) { return nexora_sanitize_choice( $value, array( '13', '14', '15', '16' ), '14' ); }
function nexora_sanitize_layout( $value ) { return nexora_sanitize_choice( $value, array( 'constrained', 'full' ), 'constrained' ); }
function nexora_sanitize_posts_per_page( $value ) { return (string) max( 1, min( 48, absint( $value ) ) ); }
function nexora_sanitize_logo_height( $value ) { return (string) max( 24, min( 160, absint( $value ) ) ); }
function nexora_sanitize_dark_mode( $value ) { return (bool) $value; }
function nexora_sanitize_dark_default( $value ) { return nexora_sanitize_choice( $value, array( 'light', 'dark', 'system' ), 'system' ); }
function nexora_sanitize_slug_list( $value ) {
    $slugs = array();
    foreach ( explode( ',', strtolower( (string) $value ) ) as $slug ) {
        $slug = sanitize_title( trim( $slug ) );
        if ( $slug ) { $slugs[] = $slug; }
    }
    return implode( ', ', array_unique( $slugs ) );
}

function nexora_trade_customize( $customizer ) {
    $customizer->add_panel( 'nexora_portal_options', array( 'title' => __( 'Trade Portal Options', 'nexora-trade' ), 'description' => __( 'Site-wide theme settings. Page content is edited in Elementor.', 'nexora-trade' ), 'priority' => 30 ) );

    /* ---- 1. Brand, colours & layout ---- */
    $customizer->add_section( 'nexora_brand_options', array( 'title' => __( 'Brand, Colours & Layout', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    foreach ( array(
        'nexora_accent_color' => array( 'Accent colour (buttons, links, highlights)', '#0E6B57' ),
        'nexora_deep_color' => array( 'Deep colour (logo tile, hero overlay, footer)', '#0E3B33' ),
        'nexora_page_background' => array( 'Page background', '#f5f8f5' ),
        'nexora_text_color' => array( 'Text colour', '#1c302b' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
        $customizer->add_control( new WP_Customize_Color_Control( $customizer, $key, array( 'label' => $config[0], 'section' => 'nexora_brand_options' ) ) );
    }
    foreach ( array(
        'nexora_content_width' => array( 'Content width', '1320', array( '1100' => 'Compact (1100px)', '1320' => 'Standard (1320px)', '1440' => 'Wide (1440px)' ), 'nexora_sanitize_width' ),
        'nexora_corner_radius' => array( 'Card corners', '9', array( '4' => 'Subtle', '9' => 'Soft', '16' => 'Rounded' ), 'nexora_sanitize_radius' ),
        'nexora_font_style' => array( 'Typography', 'modern', array( 'modern' => 'Modern (Manrope & DM Sans)', 'system' => 'System fonts' ), 'nexora_sanitize_font' ),
        'nexora_base_font_size' => array( 'Base text size', '14', array( '13' => 'Small (13px)', '14' => 'Regular (14px)', '15' => 'Comfortable (15px)', '16' => 'Large (16px)' ), 'nexora_sanitize_font_size' ),
        'nexora_page_layout' => array( 'Theme page layout', 'constrained', array( 'constrained' => 'Constrained width', 'full' => 'Full width' ), 'nexora_sanitize_layout' ),
        'nexora_logo_height' => array( 'Logo height (px)', '56', array(), 'nexora_sanitize_logo_height' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => $config[3] ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_brand_options', 'type' => $config[2] ? 'select' : 'number', 'choices' => $config[2] ) );
    }
    foreach ( array(
        'nexora_load_webfonts' => array( 'Load webfonts from Google', 'Turn off for stricter privacy or offline setups; system fonts are used instead.', true ),
        'nexora_sticky_header' => array( 'Sticky header', 'Keep the header and navigation visible while scrolling.', false ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[2], 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'description' => $config[1], 'section' => 'nexora_brand_options', 'type' => 'checkbox' ) );
    }
    $customizer->add_setting( 'nexora_brand_label', array( 'default' => 'SMART HOME', 'sanitize_callback' => 'sanitize_text_field' ) );
    $customizer->add_control( 'nexora_brand_label', array( 'label' => 'Small label beside the logo', 'description' => 'Shown when no custom logo is uploaded.', 'section' => 'nexora_brand_options', 'type' => 'text' ) );

    /* Dark-mode logo: the same upload pattern as Site Identity, but shown on dark backgrounds. */
    $customizer->add_setting( 'nexora_dark_logo', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( new WP_Customize_Image_Control( $customizer, 'nexora_dark_logo', array(
        'label' => 'Logo for dark mode',
        'description' => 'Optional. A light or white version of your logo. Shown in the header while dark mode is on, and in the footer. Leave blank to keep using the standard logo everywhere.',
        'section' => 'nexora_brand_options',
    ) ) );

    /* ---- 1b. Dark mode ---- */
    $customizer->add_section( 'nexora_dark_options', array(
        'title' => __( 'Dark Mode', 'nexora-trade' ),
        'description' => __( 'A light/dark switch in the header. Visitors can override it, and their choice is remembered in their browser.', 'nexora-trade' ),
        'panel' => 'nexora_portal_options',
    ) );
    foreach ( array(
        'nexora_dark_mode' => array( 'Enable dark mode', 'Adds the dark palette, the header switch and the dark-mode logo.', true ),
        'nexora_dark_toggle' => array( 'Show the switch in the header', 'Turn off to keep dark mode available only as the starting mode below, with no visitor control.', true ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[2], 'sanitize_callback' => 'nexora_sanitize_dark_mode' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'description' => $config[1], 'section' => 'nexora_dark_options', 'type' => 'checkbox' ) );
    }
    $customizer->add_setting( 'nexora_dark_default', array( 'default' => 'system', 'sanitize_callback' => 'nexora_sanitize_dark_default' ) );
    $customizer->add_control( 'nexora_dark_default', array(
        'label' => 'Colour scheme when a visitor first arrives',
        'description' => '“Match the visitor’s device” follows their operating system setting and keeps following it until they use the switch.',
        'section' => 'nexora_dark_options',
        'type' => 'select',
        'choices' => array( 'system' => 'Match the visitor’s device', 'light' => 'Always light', 'dark' => 'Always dark' ),
    ) );
    $customizer->add_setting( 'nexora_dark_footer_logo', array( 'default' => true, 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
    $customizer->add_control( 'nexora_dark_footer_logo', array( 'label' => 'Use the dark-mode logo in the footer', 'description' => 'The footer always has a dark background, so a light logo usually reads better there.', 'section' => 'nexora_dark_options', 'type' => 'checkbox' ) );
    foreach ( array(
        'nexora_dark_bg' => array( 'Dark mode: page background', '#101815' ),
        'nexora_dark_surface' => array( 'Dark mode: cards and panels', '#17211d' ),
        'nexora_dark_surface_2' => array( 'Dark mode: raised areas and inputs', '#1f2b25' ),
        'nexora_dark_border' => array( 'Dark mode: borders', '#2b3a32' ),
        'nexora_dark_text' => array( 'Dark mode: text', '#e7efe9' ),
        'nexora_dark_muted' => array( 'Dark mode: secondary text', '#a8b8ae' ),
        'nexora_dark_accent' => array( 'Dark mode: accent (links, highlights)', '#6cc191' ),
        'nexora_dark_header_bg' => array( 'Dark mode: header background', '#131d18' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
        $customizer->add_control( new WP_Customize_Color_Control( $customizer, $key, array( 'label' => $config[0], 'section' => 'nexora_dark_options' ) ) );
    }
    foreach ( array(
        'nexora_toggle_icon_color' => array( 'Switch: icon colour', '#335d46' ),
        'nexora_toggle_bg' => array( 'Switch: background', '#f0f6f1' ),
        'nexora_toggle_border' => array( 'Switch: border', '#e4ece7' ),
        'nexora_toggle_icon_color_dark' => array( 'Switch on dark mode: icon colour', '#d7e6dc' ),
        'nexora_toggle_bg_dark' => array( 'Switch on dark mode: background', '#22332c' ),
        'nexora_toggle_border_dark' => array( 'Switch on dark mode: border', '#31473c' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
        $customizer->add_control( new WP_Customize_Color_Control( $customizer, $key, array( 'label' => $config[0], 'section' => 'nexora_dark_options' ) ) );
    }

    /* ---- 2. Header & navigation ---- */
    $customizer->add_section( 'nexora_header_options', array( 'title' => __( 'Header & Navigation', 'nexora-trade' ), 'description' => __( 'Menu items are managed in Appearance > Menus.', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    foreach ( array(
        'nexora_portal_label' => array( 'Portal label beside logo', 'TRADE PARTNER PORTAL' ),
        'nexora_header_note' => array( 'Note in navigation bar', 'YOUR PARTNER IN SMARTER LIVING' ),
        'nexora_help_label' => array( 'Support link label', 'Partner support' ),
        'nexora_search_placeholder' => array( 'Search field placeholder', 'Search the portal...' ),
        'nexora_account_label' => array( 'Account link label', 'My Account' ),
        'nexora_login_label' => array( 'Logged-out link label', 'Log in' ),
        'nexora_logout_label' => array( 'Logged-in link label', 'Log out' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_text_field' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_header_options', 'type' => 'text' ) );
    }
    foreach ( array(
        'nexora_show_search' => array( 'Show header search', true ),
        'nexora_show_support' => array( 'Show support link', true ),
        'nexora_show_account' => array( 'Show account / login links', true ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_header_options', 'type' => 'checkbox' ) );
    }
    $customizer->add_setting( 'nexora_support_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( 'nexora_support_url', array( 'label' => 'Support page URL', 'description' => 'Leave blank to use the imported Support page.', 'section' => 'nexora_header_options', 'type' => 'url' ) );
    $customizer->add_setting( 'nexora_contact_email', array( 'default' => get_option( 'admin_email' ), 'sanitize_callback' => 'sanitize_email' ) );
    $customizer->add_control( 'nexora_contact_email', array( 'label' => 'Support form recipient', 'description' => 'Used by the demo support form. Configure SMTP with your host for reliable delivery.', 'section' => 'nexora_header_options', 'type' => 'email' ) );

    /* ---- 3. Announcement bar ---- */
    $customizer->add_section( 'nexora_topbar_options', array( 'title' => __( 'Announcement Bar', 'nexora-trade' ), 'description' => __( 'A slim bar above the header for current announcements, such as a new product launch.', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    $customizer->add_setting( 'nexora_show_topbar', array( 'default' => false, 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
    $customizer->add_control( 'nexora_show_topbar', array( 'label' => 'Show announcement bar', 'section' => 'nexora_topbar_options', 'type' => 'checkbox' ) );
    foreach ( array( 'nexora_topbar_text' => array( 'Announcement text', 'New smart home products launched' ), 'nexora_topbar_link_text' => array( 'Link text', 'Read the announcement' ) ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_text_field' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_topbar_options', 'type' => 'text' ) );
    }
    $customizer->add_setting( 'nexora_topbar_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( 'nexora_topbar_url', array( 'label' => 'Announcement link URL', 'section' => 'nexora_topbar_options', 'type' => 'url' ) );
    $customizer->add_setting( 'nexora_topbar_color', array( 'default' => '#0E3B33', 'sanitize_callback' => 'sanitize_hex_color' ) );
    $customizer->add_control( new WP_Customize_Color_Control( $customizer, 'nexora_topbar_color', array( 'label' => 'Bar background colour', 'section' => 'nexora_topbar_options' ) ) );

    /* ---- 4. Footer & contact ---- */
    $customizer->add_section( 'nexora_footer_options', array( 'title' => __( 'Footer & Contact', 'nexora-trade' ), 'description' => __( 'Footer menus can be assigned in Appearance > Menus.', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    foreach ( array(
        'nexora_footer_tagline' => array( 'Brand description', 'Making smarter living simpler, together.' ),
        'nexora_footer_help' => array( 'Support column heading text', 'Questions? We’re here to support you.' ),
        'nexora_footer_cta' => array( 'Support link text', 'Contact partner support' ),
        'nexora_footer_note' => array( 'Footer tagline note', 'SMARTER SPACES. BETTER LIVING.' ),
        'nexora_footer_copyright' => array( 'Copyright line', '© {year} {sitename}. Made for our trade partners.' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_text_field' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_footer_options', 'type' => 'text' ) );
    }
    $customizer->get_control( 'nexora_footer_copyright' )->description = 'Use {year} and {sitename} as placeholders.';
    foreach ( array(
        'nexora_footer_phone' => array( 'Phone number', '' ),
        'nexora_footer_email' => array( 'Partner email', '' ),
        'nexora_footer_address' => array( 'Address', '' ),
        'nexora_footer_hours' => array( 'Opening hours', '' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_text_field' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'description' => 'Shown in the footer support column.', 'section' => 'nexora_footer_options', 'type' => 'text' ) );
    }
    $customizer->add_setting( 'nexora_show_footer_contact', array( 'default' => true, 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
    $customizer->add_control( 'nexora_show_footer_contact', array( 'label' => 'Show contact details in footer', 'section' => 'nexora_footer_options', 'type' => 'checkbox' ) );

    /* ---- 5. Products & catalogue ---- */
    $customizer->add_section( 'nexora_catalogue_options', array( 'title' => __( 'Products & Catalogue', 'nexora-trade' ), 'description' => __( 'WooCommerce settings for a product-detail catalogue with no selling.', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    foreach ( array(
        'nexora_catalogue_mode' => array( 'Catalogue-only mode', 'Hide prices and purchasing, and redirect cart, checkout and the WooCommerce account pages.', true ),
        'nexora_show_product_inquiry' => array( 'Show “ask about this product” button', 'Adds a contact button to single product pages.', true ),
        'nexora_hide_related_products' => array( 'Hide related and upsell products', 'Keeps catalogue pages focused on product detail.', true ),
        'nexora_show_product_meta' => array( 'Show SKU and category details', 'Product meta under the description on single product pages.', false ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[2], 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'description' => $config[1], 'section' => 'nexora_catalogue_options', 'type' => 'checkbox' ) );
    }
    $customizer->add_setting( 'nexora_product_inquiry_label', array( 'default' => 'Ask about this product', 'sanitize_callback' => 'sanitize_text_field' ) );
    $customizer->add_control( 'nexora_product_inquiry_label', array( 'label' => 'Product enquiry button text', 'section' => 'nexora_catalogue_options', 'type' => 'text' ) );
    $customizer->add_setting( 'nexora_product_columns', array( 'default' => '4', 'sanitize_callback' => 'nexora_sanitize_columns' ) );
    $customizer->add_control( 'nexora_product_columns', array( 'label' => 'Products per row', 'section' => 'nexora_catalogue_options', 'type' => 'select', 'choices' => array( '3' => 'Three', '4' => 'Four', '5' => 'Five' ) ) );
    $customizer->add_setting( 'nexora_product_inquiry_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( 'nexora_product_inquiry_url', array( 'label' => 'Product enquiry destination', 'description' => 'Leave blank to use the Support page.', 'section' => 'nexora_catalogue_options', 'type' => 'url' ) );

    /* ---- 6. News, posts & private areas ---- */
    $customizer->add_section( 'nexora_content_options', array( 'title' => __( 'News & Private Areas', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    $customizer->add_setting( 'nexora_posts_per_page', array( 'default' => '10', 'sanitize_callback' => 'nexora_sanitize_posts_per_page' ) );
    $customizer->add_control( 'nexora_posts_per_page', array( 'label' => 'Announcements per page', 'section' => 'nexora_content_options', 'type' => 'number', 'input_attrs' => array( 'min' => 1, 'max' => 48 ) ) );
    $customizer->add_setting( 'nexora_show_post_dates', array( 'default' => true, 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
    $customizer->add_control( 'nexora_show_post_dates', array( 'label' => 'Show dates on announcements', 'section' => 'nexora_content_options', 'type' => 'checkbox' ) );
    $customizer->add_setting( 'nexora_private_slugs', array( 'default' => '', 'sanitize_callback' => 'nexora_sanitize_slug_list' ) );
    $customizer->add_control( 'nexora_private_slugs', array( 'label' => 'Login-required page slugs', 'description' => 'Comma-separated, for example: downloads, technical. Visitors who are not logged in are sent to the login page. For real partner access control, also use a membership plugin and protect uploaded files.', 'section' => 'nexora_content_options', 'type' => 'text' ) );

    /* ---- 7. Login & account ---- */
    $customizer->add_section( 'nexora_login_options', array( 'title' => __( 'Login & Account', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    $customizer->add_setting( 'nexora_branded_login', array( 'default' => false, 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
    $customizer->add_control( 'nexora_branded_login', array( 'label' => 'Branded login screen', 'description' => 'Apply your colours and logo to wp-login.php.', 'section' => 'nexora_login_options', 'type' => 'checkbox' ) );
    $customizer->add_setting( 'nexora_login_logo', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( new WP_Customize_Image_Control( $customizer, 'nexora_login_logo', array( 'label' => 'Login screen logo', 'section' => 'nexora_login_options' ) ) );
    $customizer->add_setting( 'nexora_login_heading', array( 'default' => 'Trade Partner Portal', 'sanitize_callback' => 'sanitize_text_field' ) );
    $customizer->add_control( 'nexora_login_heading', array( 'label' => 'Login screen heading', 'section' => 'nexora_login_options', 'type' => 'text' ) );
    $customizer->add_setting( 'nexora_login_background', array( 'default' => '#f5f8f5', 'sanitize_callback' => 'sanitize_hex_color' ) );
    $customizer->add_control( new WP_Customize_Color_Control( $customizer, 'nexora_login_background', array( 'label' => 'Login screen background', 'section' => 'nexora_login_options' ) ) );
    $customizer->add_setting( 'nexora_login_redirect', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( 'nexora_login_redirect', array( 'label' => 'Send partners to this page after login', 'description' => 'Leave blank to use the requested page or the homepage.', 'section' => 'nexora_login_options', 'type' => 'url' ) );

    /* ---- 8. 404 & notices ---- */
    $customizer->add_section( 'nexora_notice_options', array( 'title' => __( '404 Page & Notices', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    foreach ( array(
        'nexora_404_heading' => array( 'Page not found heading', 'We couldn’t find that page.' ),
        'nexora_404_text' => array( 'Page not found text', 'The link may have changed. Try the portal navigation or search below.' ),
        'nexora_404_button' => array( 'Button text', 'Back to the portal' ),
    ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_text_field' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_notice_options', 'type' => 'text' ) );
    }
    $customizer->add_setting( 'nexora_show_sample_notice', array( 'default' => true, 'sanitize_callback' => 'nexora_sanitize_checkbox' ) );
    $customizer->add_control( 'nexora_show_sample_notice', array( 'label' => 'Show sample-content reminder to administrators', 'description' => 'A small bar shown only to logged-in administrators while demo content is installed. Turn it off before handing the site over.', 'section' => 'nexora_notice_options', 'type' => 'checkbox' ) );

    /* ---- 9. Default homepage ---- */
    $customizer->add_section( 'nexora_portal', array( 'title' => __( 'Default Homepage', 'nexora-trade' ), 'description' => __( 'Applies to the theme’s built-in homepage only, before you build one in Elementor.', 'nexora-trade' ), 'panel' => 'nexora_portal_options' ) );
    $customizer->add_setting( 'nexora_hero_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $customizer->add_control( new WP_Customize_Image_Control( $customizer, 'nexora_hero_image', array( 'label' => 'Homepage hero image', 'section' => 'nexora_portal' ) ) );
    $customizer->add_setting( 'nexora_hero_shortcode', array( 'default' => '', 'sanitize_callback' => 'wp_kses_post' ) );
    $customizer->add_control( 'nexora_hero_shortcode', array( 'label' => 'Homepage hero slider shortcode', 'description' => 'Optional. Paste a Smart Slider 3 shortcode, e.g. [smartslider3 slider="1"]. When set, it replaces the static hero on the built-in homepage. For Elementor pages, add the same shortcode with an Elementor Shortcode widget.', 'section' => 'nexora_portal', 'type' => 'textarea' ) );
    foreach ( array( 'nexora_hero_heading' => array( 'Hero heading', 'Welcome to your Trade Partner Portal.' ), 'nexora_hero_description' => array( 'Hero description', 'Everything you need to promote, distribute and support our smart home product range.' ) ) as $key => $config ) {
        $customizer->add_setting( $key, array( 'default' => $config[1], 'sanitize_callback' => 'sanitize_text_field' ) );
        $customizer->add_control( $key, array( 'label' => $config[0], 'section' => 'nexora_portal', 'type' => 'text' ) );
    }
    foreach ( array( 'nexora_catalogue_url' => 'Product catalogue URL', 'nexora_installation_url' => 'Installation guide URL', 'nexora_brochure_url' => 'Marketing brochure URL' ) as $key => $label ) {
        $customizer->add_setting( $key, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
        $customizer->add_control( $key, array( 'label' => $label, 'section' => 'nexora_portal', 'type' => 'url' ) );
    }
}
add_action( 'customize_register', 'nexora_trade_customize' );

function nexora_sanitize_columns( $value ) { return nexora_sanitize_choice( $value, array( '3', '4', '5' ), '4' ); }

function nexora_colour( $key, $fallback ) { return sanitize_hex_color( get_theme_mod( $key, $fallback ) ) ?: $fallback; }

/** Emit the design tokens and option-driven rules for the theme and Elementor demo styles. */
function nexora_trade_option_styles() {
    $accent = nexora_colour( 'nexora_accent_color', '#0E6B57' );
    $deep = nexora_colour( 'nexora_deep_color', '#0E3B33' );
    $background = nexora_colour( 'nexora_page_background', '#f5f8f5' );
    $text = nexora_colour( 'nexora_text_color', '#1c302b' );
    $topbar = nexora_colour( 'nexora_topbar_color', '#0E3B33' );
    $width = nexora_sanitize_width( (string) get_theme_mod( 'nexora_content_width', '1320' ) );
    $radius = nexora_sanitize_radius( (string) get_theme_mod( 'nexora_corner_radius', '9' ) );
    $size = nexora_sanitize_font_size( (string) get_theme_mod( 'nexora_base_font_size', '14' ) );
    $logo = nexora_sanitize_logo_height( (string) get_theme_mod( 'nexora_logo_height', '56' ) );
    $system = 'system' === nexora_sanitize_font( get_theme_mod( 'nexora_font_style', 'modern' ) ) || ! get_theme_mod( 'nexora_load_webfonts', true );
    $body_font = $system ? "system-ui, -apple-system, 'Segoe UI', sans-serif" : "'DM Sans', Arial, sans-serif";
    $heading_font = $system ? "system-ui, -apple-system, 'Segoe UI', sans-serif" : 'Manrope, sans-serif';
    $css = ':root{--nexora-accent:' . $accent . ';--nexora-deep:' . $deep . ';--nexora-background:' . $background . ';--nexora-text:' . $text . ';--nexora-width:' . $width . 'px;--nexora-radius:' . $radius . 'px;--nexora-font-size:' . $size . 'px;--nexora-logo-height:' . $logo . 'px;--nexora-topbar:' . $topbar . ';--nexora-body-font:' . $body_font . ';--nexora-heading-font:' . $heading_font . '}';
    $css .= 'body{background:var(--nexora-background);color:var(--nexora-text);font-family:var(--nexora-body-font);font-size:var(--nexora-font-size)}';
    $css .= '.shell{max-width:var(--nexora-width)}.custom-logo{max-height:var(--nexora-logo-height);width:auto}';
    $css .= '@media(min-width:801px){.site-header .header-top{height:calc(78px + max(0px, var(--nexora-logo-height) - 56px))}}';
    $css .= '@media(max-width:800px){.custom-logo{max-height:min(var(--nexora-logo-height),46px)}}';
    $css .= '.custom-logo-link.has-light-logo .custom-logo-dark{display:none}html[data-nexora-theme="dark"] .custom-logo-link.has-light-logo .custom-logo-light{display:none}html[data-nexora-theme="dark"] .custom-logo-link.has-light-logo .custom-logo-dark{display:block}';
    $css .= '.brand-symbol,.site-footer,.portal-topbar{background:var(--nexora-deep)}.portal-topbar{background:var(--nexora-topbar)}';
    $css .= '.button-dark,.woocommerce a.button,.woocommerce button.button,.nexora-partner-form button{background:var(--nexora-accent)}';
    $css .= '.text-link,.mini-link,.entry-content a,.nexora-sample-product a{color:var(--nexora-accent)}';
    $css .= 'h1,h2,h3,.hero h1,.page-intro h1,.post-card h2,.detail-content h1,.article-card h1,.product-card-content h3,.library-card h3,.training-card h3,.section-heading h2,.tool-card h3,.support-banner h2{font-family:var(--nexora-heading-font)}';
    $css .= '.hero,.tool-card,.updates-panel,.resources-panel,.support-banner,.post-card,.form-card,.info-card,.account-card,.article-card,.library-card,.training-card,.product-card,.search-result,.news-list .announcement-row,.nexora-e-hero,.nexora-e-tool-card,.nexora-e-resource-card,.nexora-e-cta,.nexora-e-split-column,.nexora-e-support-form,.nexora-e-support-info,.nexora-sample-product,.nexora-news-list{border-radius:var(--nexora-radius)}';
    if ( 'full' === nexora_sanitize_layout( get_theme_mod( 'nexora_page_layout', 'constrained' ) ) ) { $css .= '.content-page,.woocommerce-content{width:100%;max-width:none;padding-left:32px;padding-right:32px}'; }
    if ( get_theme_mod( 'nexora_sticky_header', false ) ) { $css .= '.site-header{position:sticky;top:0;box-shadow:0 1px 10px rgba(20,40,30,.07)}'; }
    if ( ! get_theme_mod( 'nexora_show_post_dates', true ) ) { $css .= '.post-card .eyebrow,.post-card .article-date{display:none}'; }
    $css .= '.woocommerce ul.products.columns-3 li.product,.woocommerce-page ul.products.columns-3 li.product{width:30.75%}.woocommerce ul.products.columns-5 li.product{width:16.95%}';
    wp_add_inline_style( 'nexora-trade-style', $css );
}
add_action( 'wp_enqueue_scripts', 'nexora_trade_option_styles', 20 );
