<?php
/** Native Elementor Free demo layouts. Classic sections/columns are supported by Elementor without Pro or experiments. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nexora_el_id() { return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 8 ); }
function nexora_el_widget( $type, $settings = array(), $class = '' ) {
    if ( $class ) { $settings['_css_classes'] = $class; }
    return array( 'id' => nexora_el_id(), 'elType' => 'widget', 'widgetType' => $type, 'isInner' => false, 'settings' => $settings, 'elements' => array() );
}
function nexora_el_heading( $text, $tag = 'h2', $class = '' ) {
    return nexora_el_widget( 'heading', array( 'title' => $text, 'header_size' => $tag ), $class );
}
function nexora_el_text( $text, $class = '' ) {
    return nexora_el_widget( 'text-editor', array( 'editor' => '<p>' . esc_html( $text ) . '</p>' ), $class );
}
function nexora_el_button( $text, $url, $class = '' ) {
    return nexora_el_widget( 'button', array( 'text' => $text, 'link' => array( 'url' => $url, 'is_external' => '', 'nofollow' => '' ), 'size' => 'sm' ), $class );
}
function nexora_el_image( $asset, $class = '' ) {
    return nexora_el_widget( 'image', array( 'image' => array( 'url' => $asset['url'], 'id' => $asset['id'] ), 'image_size' => 'large' ), $class );
}
function nexora_el_icon( $icon ) {
    return nexora_el_widget( 'icon', array( 'selected_icon' => array( 'value' => $icon, 'library' => 'fa-solid' ), 'view' => 'default' ), 'nexora-e-icon' );
}
function nexora_el_shortcode( $shortcode ) { return nexora_el_widget( 'shortcode', array( 'shortcode' => $shortcode ), 'nexora-e-shortcode' ); }
function nexora_el_column( $elements, $width = 100, $class = '' ) {
    return array( 'id' => nexora_el_id(), 'elType' => 'column', 'isInner' => false, 'settings' => array( '_column_size' => $width, '_inline_size' => null, 'css_classes' => $class ), 'elements' => $elements );
}
function nexora_el_section( $columns, $class = '', $settings = array() ) {
    $defaults = array( 'layout' => 'boxed', 'content_width' => array( 'unit' => 'px', 'size' => 1200 ), 'css_classes' => trim( 'nexora-e-section ' . $class ) );
    return array( 'id' => nexora_el_id(), 'elType' => 'section', 'isInner' => false, 'settings' => array_merge( $defaults, $settings ), 'elements' => $columns );
}
function nexora_el_intro( $eyebrow, $title, $description ) {
    return nexora_el_section( array( nexora_el_column( array(
        nexora_el_heading( $eyebrow, 'p', 'nexora-e-eyebrow' ),
        nexora_el_heading( $title, 'h1', 'nexora-e-page-title' ),
        nexora_el_text( $description, 'nexora-e-lead' ),
    ) ) ), 'nexora-e-intro' );
}
function nexora_el_section_title( $eyebrow, $title, $description = '' ) {
    $widgets = array( nexora_el_heading( $eyebrow, 'p', 'nexora-e-eyebrow' ), nexora_el_heading( $title, 'h2', 'nexora-e-section-title' ) );
    if ( $description ) { $widgets[] = nexora_el_text( $description, 'nexora-e-description' ); }
    return nexora_el_section( array( nexora_el_column( $widgets ) ), 'nexora-e-title-row' );
}
function nexora_el_tool_card( $title, $text, $url, $icon ) {
    return nexora_el_column( array( nexora_el_icon( $icon ), nexora_el_heading( $title, 'h3', 'nexora-e-card-title' ), nexora_el_text( $text, 'nexora-e-card-text' ), nexora_el_button( 'Explore', $url, 'nexora-e-link-button' ) ), 25, 'nexora-e-tool-card' );
}
function nexora_el_resource_card( $label, $title, $text, $url, $button = 'Download PDF', $image = null ) {
    $widgets = array();
    if ( $image ) { $widgets[] = nexora_el_image( $image, 'nexora-e-card-image' ); }
    $widgets[] = nexora_el_heading( $label, 'p', 'nexora-e-card-label' );
    $widgets[] = nexora_el_heading( $title, 'h3', 'nexora-e-card-title' );
    $widgets[] = nexora_el_text( $text, 'nexora-e-card-text' );
    $widgets[] = nexora_el_button( $button, $url, 'nexora-e-link-button' );
    return nexora_el_column( $widgets, 33, 'nexora-e-resource-card' );
}
function nexora_el_cta( $urls ) {
    return nexora_el_section( array(
        nexora_el_column( array( nexora_el_heading( 'WE’RE HERE FOR YOU', 'p', 'nexora-e-eyebrow' ), nexora_el_heading( 'Need a hand? Let’s talk.', 'h2', 'nexora-e-section-title' ), nexora_el_text( 'Whatever you’re working on, our team is here to help.', 'nexora-e-description' ) ), 70 ),
        nexora_el_column( array( nexora_el_button( 'Contact support →', $urls['support'], 'nexora-e-primary-button' ) ), 30, 'nexora-e-cta-action' ),
    ), 'nexora-e-cta' );
}
function nexora_el_resource_sections( $assets, $urls, $filter = 'all' ) {
    $cards = array(
        array( 'PRODUCT CATALOGUE', 'Product catalogue 2026', 'A closer look at our complete smart home range.', $assets['product-catalogue-2026.pdf']['url'], 'marketing', 'product-hub.jpg' ),
        array( 'INSTALLATION GUIDE', 'Smart home installation guide', 'A practical starting point for your next installation.', $assets['smart-home-installation-guide.pdf']['url'], 'technical', 'installation-guide.jpg' ),
        array( 'MARKETING BROCHURE', 'Latest marketing brochure', 'Introduce your customers to smarter everyday living.', $assets['marketing-brochure.pdf']['url'], 'marketing', 'portal-hero.jpg' ),
        array( 'SPECIFICATIONS', 'Product specification overview', 'A useful reference for your next project.', $assets['smart-home-installation-guide.pdf']['url'], 'technical', 'product-panel.jpg' ),
        array( 'BRAND MATERIALS', 'Brand and image guidelines', 'Present your smart living story consistently.', $assets['marketing-brochure.pdf']['url'], 'marketing', 'portal-hero.jpg' ),
        array( 'PROJECT CHECKLIST', 'Commissioning checklist', 'Get ready for a smooth connected home installation.', $assets['smart-home-installation-guide.pdf']['url'], 'technical', 'product-module.jpg' ),
    );
    $chosen = array();
    foreach ( $cards as $card ) {
        if ( 'all' !== $filter && $filter !== $card[4] ) { continue; }
        $chosen[] = nexora_el_resource_card( $card[0], $card[1], $card[2], $card[3], 'Download PDF', $assets[ $card[5] ] );
    }
    $sections = array();
    foreach ( array_chunk( $chosen, 3 ) as $row ) { $sections[] = nexora_el_section( $row, 'nexora-e-card-row' ); }
    return $sections;
}

/** Build the page tree from individually editable Elementor widgets. */
function nexora_elementor_demo_layout( $slug, $urls, $assets ) {
    $hero = array(
        nexora_el_heading( 'THE TRADE PARTNER PORTAL', 'p', 'nexora-e-hero-eyebrow' ),
        nexora_el_heading( 'Welcome to your<br>Trade Partner Portal.', 'h1', 'nexora-e-hero-title' ),
        nexora_el_text( 'Everything you need to promote, distribute and support our smart home product range.', 'nexora-e-hero-text' ),
        nexora_el_button( 'Explore products →', $urls['products'], 'nexora-e-hero-button' ),
    );
    if ( 'home' === $slug ) {
        return array(
            nexora_el_section( array( nexora_el_column( $hero, 60, 'nexora-e-hero-content' ), nexora_el_column( array(), 40 ) ), 'nexora-e-hero', array(
                'background_background' => 'classic', 'background_image' => array( 'id' => $assets['portal-hero.jpg']['id'], 'url' => $assets['portal-hero.jpg']['url'] ), 'background_position' => 'center center', 'background_size' => 'cover',
                'background_overlay_background' => 'classic', 'background_overlay_color' => '#0E3B33', 'background_overlay_opacity' => array( 'unit' => 'px', 'size' => 0.62 ),
            ) ),
            nexora_el_section_title( 'YOUR PARTNER TOOLKIT', 'Everything you need, all in one place.', 'Quick access to the tools and resources that help you do more.' ),
            nexora_el_section( array(
                nexora_el_tool_card( 'Explore Products', 'Browse our complete product range', $urls['products'], 'fas fa-box-open' ),
                nexora_el_tool_card( 'Technical Documents', 'Find specifications and manuals', $urls['technical'], 'fas fa-file-alt' ),
                nexora_el_tool_card( 'Marketing Assets', 'Download images and brochures', $urls['marketing'], 'fas fa-images' ),
                nexora_el_tool_card( 'Training Centre', 'Learn about products and installation', $urls['training'], 'fas fa-book-open' ),
            ), 'nexora-e-tool-row' ),
            nexora_el_section( array(
                nexora_el_column( array( nexora_el_heading( 'THE LATEST', 'p', 'nexora-e-eyebrow' ), nexora_el_heading( 'Latest announcements', 'h2', 'nexora-e-section-title' ), nexora_el_text( 'Stay in the know with news from our team.', 'nexora-e-description' ), nexora_el_shortcode( '[nexora_latest_news limit="3"]' ), nexora_el_button( 'View all news →', $urls['news'], 'nexora-e-link-button' ) ), 50, 'nexora-e-split-column' ),
                nexora_el_column( array( nexora_el_heading( 'HAND-PICKED FOR YOU', 'p', 'nexora-e-eyebrow' ), nexora_el_heading( 'Featured resources', 'h2', 'nexora-e-section-title' ), nexora_el_text( 'The essentials, ready to download.', 'nexora-e-description' ), nexora_el_heading( 'Product catalogue 2026', 'h3', 'nexora-e-featured-title' ), nexora_el_button( 'Download catalogue ↓', $assets['product-catalogue-2026.pdf']['url'], 'nexora-e-link-button' ), nexora_el_heading( 'Smart home installation guide', 'h3', 'nexora-e-featured-title' ), nexora_el_button( 'Download guide ↓', $assets['smart-home-installation-guide.pdf']['url'], 'nexora-e-link-button' ), nexora_el_heading( 'Latest marketing brochure', 'h3', 'nexora-e-featured-title' ), nexora_el_button( 'Download brochure ↓', $assets['marketing-brochure.pdf']['url'], 'nexora-e-link-button' ) ), 50, 'nexora-e-split-column nexora-e-featured' ),
            ), 'nexora-e-split' ),
            nexora_el_cta( $urls ),
        );
    }
    if ( 'products' === $slug ) {
        return array(
            nexora_el_intro( 'EXPLORE THE RANGE', 'Products', 'Discover the smart home products designed to make connected living feel effortless.' ),
            nexora_el_section_title( 'DESIGNED FOR SMARTER LIVING', 'Meet the range', 'Explore controllers, lighting and everyday comfort in one connected collection.' ),
            nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[nexora_products]' ) ) ) ), 'nexora-e-catalogue' ),
            nexora_el_cta( $urls ),
        );
    }
    if ( 'technical' === $slug || 'marketing' === $slug || 'downloads' === $slug ) {
        $intro = array(
            'technical' => array( 'KNOWLEDGE & EXPERTISE', 'Technical Documents', 'Find the specifications, guides and practical resources to support every project.' ),
            'marketing' => array( 'MADE TO SHARE', 'Marketing Assets', 'Explore brochures, brand materials and resources to help you tell the smart living story.' ),
            'downloads' => array( 'YOUR RESOURCE LIBRARY', 'Downloads', 'All the useful information you need, together in one easy-to-find place.' ),
        );
        $sections = array( nexora_el_intro( $intro[$slug][0], $intro[$slug][1], $intro[$slug][2] ) );
        if ( defined( 'APR_VERSION' ) ) {
            if ( 'downloads' === $slug ) {
                $sections[] = nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[apr_resources]' ) ) ) ), 'nexora-e-catalogue' );
            } elseif ( 'technical' === $slug ) {
                $sections[] = nexora_el_section_title( 'TECHNICAL LIBRARY', 'Datasheets & installation guides', 'Managed in Resources → Add New.' );
                $sections[] = nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[apr_resources type="datasheet"]' ) ) ) ), 'nexora-e-catalogue' );
                $sections[] = nexora_el_section_title( 'SOFTWARE & UPDATES', 'Latest firmware', 'Release notes for installed Aqara devices.' );
                $sections[] = nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[apr_firmware]' ) ) ) ), 'nexora-e-catalogue' );
            } else {
                $sections[] = nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[apr_resources type="brochure"]' ) ) ) ), 'nexora-e-catalogue' );
            }
            $sections[] = nexora_el_cta( $urls );
            return $sections;
        }
        return array_merge( $sections, array( nexora_el_section_title( 'PARTNER RESOURCES', 'Resources for you', 'Sample PDFs are included so you can try the downloads. Replace them with your approved documents.' ) ), nexora_el_resource_sections( $assets, $urls, 'downloads' === $slug ? 'all' : $slug ), array( nexora_el_cta( $urls ) ) );
    }
    if ( 'training' === $slug ) {
        return array(
            nexora_el_intro( 'GROW YOUR KNOWLEDGE', 'Training Centre', 'Learn about our products, connected living and the essentials of a successful installation.' ),
            nexora_el_section_title( 'LEARN AT YOUR OWN PACE', 'Build your product confidence', 'Useful starting points for every member of your team.' ),
            nexora_el_section( array(
                nexora_el_resource_card( 'GETTING STARTED', 'Getting started with smart home', 'Get to know the essentials of a connected home and the product range.', $assets['product-catalogue-2026.pdf']['url'], 'View resource', $assets['portal-hero.jpg'] ),
                nexora_el_resource_card( 'INSTALLATION', 'Installation essentials', 'Prepare for a successful project with our installation essentials.', $assets['smart-home-installation-guide.pdf']['url'], 'View resource', $assets['installation-guide.jpg'] ),
                nexora_el_resource_card( 'PRODUCT KNOWLEDGE', 'Introducing the range', 'Feel confident explaining smart living to your customers.', $assets['marketing-brochure.pdf']['url'], 'View resource', $assets['product-hub.jpg'] ),
            ), 'nexora-e-card-row' ),
            nexora_el_cta( $urls ),
        );
    }
    if ( 'news' === $slug ) {
        return array( nexora_el_intro( 'THE LATEST FROM OUR TEAM', 'News & updates', 'The latest product news, updates and announcements for our trade partners.' ), nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[nexora_latest_news limit="12"]' ) ) ) ), 'nexora-e-news-page' ), nexora_el_cta( $urls ) );
    }
    if ( 'support' === $slug ) {
        return array(
            nexora_el_intro( 'WE’RE HERE FOR YOU', 'Partner Support', 'Have a question? Let us know how we can help with your next project.' ),
            nexora_el_section( array(
                nexora_el_column( array( nexora_el_heading( 'Send us a message', 'h2', 'nexora-e-section-title' ), nexora_el_text( 'Tell us a little about what you need and we’ll point you in the right direction.', 'nexora-e-description' ), nexora_el_shortcode( '[nexora_partner_support]' ) ), 66, 'nexora-e-support-form' ),
                nexora_el_column( array( nexora_el_heading( 'HERE TO HELP', 'p', 'nexora-e-eyebrow' ), nexora_el_heading( 'Every step of the way.', 'h2', 'nexora-e-section-title' ), nexora_el_text( 'From product information to technical resources, we’re here to make working together easier.', 'nexora-e-description' ), nexora_el_button( 'Explore technical documents →', $urls['technical'], 'nexora-e-link-button' ), nexora_el_button( 'Visit the Training Centre →', $urls['training'], 'nexora-e-link-button' ) ), 33, 'nexora-e-support-info' ),
            ), 'nexora-e-support' ),
            nexora_el_section_title( 'COMMON QUESTIONS', 'Installer FAQ', 'Quick answers before opening a ticket.' ),
            nexora_el_section( array( nexora_el_column( array( nexora_el_shortcode( '[aqara_faq]' ) ) ) ), 'nexora-e-catalogue' ),
        );
    }
    return array();
}
