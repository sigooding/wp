<?php
/** Logo helpers: one logo for light backgrounds, an optional one for dark backgrounds. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** URL of the logo chosen for dark backgrounds (dark mode, footer). */
function nexora_trade_dark_logo_url() {
    $url = get_theme_mod( 'nexora_dark_logo', '' );
    return $url ? esc_url_raw( $url ) : '';
}

/** URL of the standard (light background) logo. */
function nexora_trade_light_logo_url() {
    $id = (int) get_theme_mod( 'custom_logo', 0 );
    if ( ! $id ) { return ''; }
    $url = wp_get_attachment_image_url( $id, 'full' );
    return $url ? $url : '';
}

/** Build an <img> for a logo URL, preferring the attachment for srcset support. */
function nexora_trade_logo_image( $url, $extra_classes = '' ) {
    $url = (string) $url;
    if ( '' === $url ) { return ''; }

    $class = trim( 'custom-logo ' . $extra_classes );
    $alt   = get_bloginfo( 'name' );

    $id = attachment_url_to_postid( $url );
    if ( $id ) {
        $img = wp_get_attachment_image( $id, 'full', false, array( 'class' => $class, 'alt' => $alt ) );
        if ( $img ) { return $img; }
    }

    return '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
}

/**
 * Header logo markup. Renders the standard logo plus, when one has been
 * uploaded, a second image used while dark mode is active.
 */
function nexora_trade_site_logo() {
    $light = nexora_trade_light_logo_url();
    $dark  = ( function_exists( 'nexora_trade_dark_enabled' ) && nexora_trade_dark_enabled() ) ? nexora_trade_dark_logo_url() : '';

    if ( ! $light && ! $dark ) { return ''; }
    // Never show the same file twice.
    if ( $dark && $light === $dark ) { $dark = ''; }

    $classes = 'custom-logo-link' . ( $light && $dark ? ' has-light-logo' : '' );
    $html    = '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
    if ( $light ) { $html .= nexora_trade_logo_image( $light, 'custom-logo-light' ); }
    if ( $dark ) { $html .= nexora_trade_logo_image( $dark, 'custom-logo-dark' ); }
    $html   .= '</a>';

    return $html;
}
