<?php
/**
 * Dark mode: visitor toggle, saved preference, system default and option-driven tokens.
 *
 * The colour scheme is applied with [data-nexora-theme="dark"] on <html>. An inline
 * script sets that attribute before first paint so there is no white flash, and the
 * option styles (inc/customizer.php) supply the dark palette as CSS variables.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Is the theme's dark mode feature switched on in the Customizer? */
function nexora_trade_dark_enabled() {
    return (bool) get_theme_mod( 'nexora_dark_mode', true );
}

/** Allowed starting modes for visitors: light, dark or follow the operating system. */
function nexora_trade_dark_default_mode() {
    $mode = (string) get_theme_mod( 'nexora_dark_default', 'system' );
    return in_array( $mode, array( 'light', 'dark', 'system' ), true ) ? $mode : 'system';
}

/** Show the sun/moon toggle in the header? */
function nexora_trade_dark_toggle_enabled() {
    return nexora_trade_dark_enabled() && get_theme_mod( 'nexora_dark_toggle', true );
}

/**
 * Colours of the toggle buttons. They sit on the header, which is light in light mode
 * and dark in dark mode, so each mode gets its own pair.
 */
function nexora_trade_dark_toggle_colours() {
    return array(
        'light' => array(
            'icon'   => nexora_colour( 'nexora_toggle_icon_color', '#335d46' ),
            'bg'     => nexora_colour( 'nexora_toggle_bg', '#f0f6f1' ),
            'border' => nexora_colour( 'nexora_toggle_border', '#e4ece7' ),
        ),
        'dark'  => array(
            'icon'   => nexora_colour( 'nexora_toggle_icon_color_dark', '#d7e6dc' ),
            'bg'     => nexora_colour( 'nexora_toggle_bg_dark', '#22332c' ),
            'border' => nexora_colour( 'nexora_toggle_border_dark', '#31473c' ),
        ),
    );
}

/** Inline CSS for the toggle. Kept separate so the toggle survives pages without option styles. */
function nexora_trade_dark_toggle_styles() {
    $c  = nexora_trade_dark_toggle_colours();
    $css = '.theme-toggle{display:inline-grid;place-items:center;width:35px;height:35px;padding:0;flex-shrink:0;border-radius:50%;cursor:pointer;line-height:1;'
         . 'border:1px solid ' . $c['light']['border'] . ';background:' . $c['light']['bg'] . ';color:' . $c['light']['icon'] . '}'
         . '.theme-toggle:hover{filter:brightness(.96)}'
         . '.theme-toggle svg{width:17px;height:17px;display:block}'
         . '.theme-toggle .theme-toggle-sun{display:none}'
         . '[data-nexora-theme="dark"] .theme-toggle .theme-toggle-sun{display:block}'
         . '[data-nexora-theme="dark"] .theme-toggle .theme-toggle-moon{display:none}';
    $css .= '[data-nexora-theme="dark"] .theme-toggle{border-color:' . $c['dark']['border']
          . ';background:' . $c['dark']['bg'] . ';color:' . $c['dark']['icon'] . '}';
    return $css;
}

/** The dark palette as CSS custom properties, plus component rules that need explicit colours. */
function nexora_trade_dark_css() {
    $map = array(
        'nexora_dark_bg'         => array( '--nx-dark-bg', '#101815' ),
        'nexora_dark_surface'    => array( '--nx-dark-surface', '#17211d' ),
        'nexora_dark_surface_2'  => array( '--nx-dark-surface-2', '#1f2b25' ),
        'nexora_dark_border'     => array( '--nx-dark-border', '#2b3a32' ),
        'nexora_dark_text'       => array( '--nx-dark-text', '#e7efe9' ),
        'nexora_dark_muted'      => array( '--nx-dark-muted', '#a8b8ae' ),
        'nexora_dark_accent'     => array( '--nx-dark-accent', '#6cc191' ),
        'nexora_dark_header_bg'  => array( '--nx-dark-header-bg', '#131d18' ),
    );

    $vars = '';
    foreach ( $map as $key => $token ) {
        $fallback = $token[1];
        $vars    .= $token[0] . ':' . nexora_colour( $key, $fallback ) . ';';
    }

    $css  = '[data-nexora-theme="dark"]{' . $vars . 'color-scheme:dark}';
    $css .= '[data-nexora-theme="dark"] body{background:var(--nx-dark-bg);color:var(--nx-dark-text)}';

    /* Header, navigation and search */
    $css .= '[data-nexora-theme="dark"] .site-header{background:var(--nx-dark-header-bg);border-bottom-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .navigation-bar{border-top-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .header-divider{background:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .portal-name,[data-nexora-theme="dark"] .portal-name strong,[data-nexora-theme="dark"] .nav-note{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .portal-nav a,[data-nexora-theme="dark"] .mobile-account-links a,[data-nexora-theme="dark"] .logout-link{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .portal-nav a:hover,[data-nexora-theme="dark"] .portal-nav .current-menu-item a{color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .header-search{background:var(--nx-dark-surface-2);border-color:var(--nx-dark-border);color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .header-search input{color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .header-search button{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .header-help small,[data-nexora-theme="dark"] .account-link small{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .header-help strong,[data-nexora-theme="dark"] .account-link strong{color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .round-icon{border-color:var(--nx-dark-border);color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .account-link .round-icon{background:var(--nx-dark-surface-2)}';
    $css .= '[data-nexora-theme="dark"] .brand-name strong{color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .brand-name small{color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .menu-toggle{background:var(--nx-dark-surface-2);color:var(--nx-dark-text)}';

    /* Cards, panels and lists */
    $css .= '[data-nexora-theme="dark"] .tool-card,[data-nexora-theme="dark"] .updates-panel,[data-nexora-theme="dark"] .resources-panel,'
          . '[data-nexora-theme="dark"] .post-card,[data-nexora-theme="dark"] .demo-card,[data-nexora-theme="dark"] .aqara-faq-item,'
          . '[data-nexora-theme="dark"] .aqara-release,[data-nexora-theme="dark"] .nexora-news-list,[data-nexora-theme="dark"] .nexora-sample-product,'
          . '[data-nexora-theme="dark"] .nexora-e-tool-card,[data-nexora-theme="dark"] .nexora-e-resource-card,'
          . '[data-nexora-theme="dark"] .nexora-e-split-column,[data-nexora-theme="dark"] .nexora-e-support-form,'
          . '[data-nexora-theme="dark"] .nexora-e-support-info,[data-nexora-theme="dark"] .woocommerce ul.products li.product,'
          . '[data-nexora-theme="dark"] .woocommerce div.product'
          . '{background:var(--nx-dark-surface);border-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .tool-card:hover{border-color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .tool-card h3,[data-nexora-theme="dark"] .section-heading h2,[data-nexora-theme="dark"] .support-banner h2,'
          . '[data-nexora-theme="dark"] .post-card h2,[data-nexora-theme="dark"] .announcement-copy strong,[data-nexora-theme="dark"] .resource-copy strong,'
          . '[data-nexora-theme="dark"] .content-page h1,[data-nexora-theme="dark"] .aqara-release h3,[data-nexora-theme="dark"] .nexora-sample-product h3'
          . '{color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .tool-card p,[data-nexora-theme="dark"] .section-heading>div>p:last-child,'
          . '[data-nexora-theme="dark"] .tool-number,[data-nexora-theme="dark"] .announcement-copy em,[data-nexora-theme="dark"] .resource-copy em,'
          . '[data-nexora-theme="dark"] .support-banner p:last-child,[data-nexora-theme="dark"] .home-bottom-note,[data-nexora-theme="dark"] .breadcrumbs,'
          . '[data-nexora-theme="dark"] .aqara-faq-item p,[data-nexora-theme="dark"] .aqara-release ul,[data-nexora-theme="dark"] .aqara-release-product,'
          . '[data-nexora-theme="dark"] .aqara-demo-note'
          . '{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .tool-icon{background:var(--nx-dark-surface-2);color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .date-tile{background:var(--nx-dark-surface-2);border-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .date-tile strong,[data-nexora-theme="dark"] .date-tile small,'
          . '[data-nexora-theme="dark"] .announcement-copy small,[data-nexora-theme="dark"] .resource-copy small'
          . '{color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .announcement-row,[data-nexora-theme="dark"] .resource-row,'
          . '[data-nexora-theme="dark"] .nexora-news-item,[data-nexora-theme="dark"] .aqara-faq-item summary,'
          . '[data-nexora-theme="dark"] .nexora-e-featured .nexora-e-featured-title'
          . '{border-bottom-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .download-icon{background:var(--nx-dark-surface-2);color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .support-banner,[data-nexora-theme="dark"] .nexora-e-cta{background:var(--nx-dark-surface-2);border-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .support-icon{background:var(--nx-dark-border);color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .text-link,[data-nexora-theme="dark"] .eyebrow,[data-nexora-theme="dark"] .card-arrow,'
          . '[data-nexora-theme="dark"] .row-arrow,[data-nexora-theme="dark"] .entry-content a,[data-nexora-theme="dark"] .breadcrumbs a,'
          . '[data-nexora-theme="dark"] .aqara-release a,[data-nexora-theme="dark"] .nexora-e-link-button .elementor-button,'
          . '[data-nexora-theme="dark"] .demo-card a,[data-nexora-theme="dark"] .nexora-404 .eyebrow,'
          . '[data-nexora-theme="dark"] .aqara-faq-group h3,[data-nexora-theme="dark"] .nexora-e-eyebrow .elementor-heading-title,'
          . '[data-nexora-theme="dark"] .nexora-e-card-label .elementor-heading-title,[data-nexora-theme="dark"] .nexora-sample-product a,'
          . '[data-nexora-theme="dark"] .nexora-sample-product small,[data-nexora-theme="dark"] .aqara-faq-item summary::after'
          . '{color:var(--nx-dark-accent)}';
    // Native form controls follow color-scheme; keep text typed by the visitor legible.
    $css .= '[data-nexora-theme="dark"] input,[data-nexora-theme="dark"] textarea,[data-nexora-theme="dark"] select{color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] input::placeholder,[data-nexora-theme="dark"] textarea::placeholder{color:#8b9b92;opacity:1}';

    /* Elementor demo layouts (assets/elementor-demo.css hard-codes light colours) */
    $css .= '[data-nexora-theme="dark"] .nexora-e-page-title .elementor-heading-title,'
          . '[data-nexora-theme="dark"] .nexora-e-section-title .elementor-heading-title,'
          . '[data-nexora-theme="dark"] .nexora-e-card-title .elementor-heading-title,'
          . '[data-nexora-theme="dark"] .nexora-e-featured-title .elementor-heading-title'
          . '{color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .nexora-e-lead,[data-nexora-theme="dark"] .nexora-e-description,'
          . '[data-nexora-theme="dark"] .nexora-e-card-text,[data-nexora-theme="dark"] .nexora-news-copy span,'
          . '[data-nexora-theme="dark"] .nexora-news-arrow,[data-nexora-theme="dark"] .nexora-demo-disclaimer,'
          . '[data-nexora-theme="dark"] .nexora-shortcode-empty,[data-nexora-theme="dark"] .nexora-e-card-text p'
          . '{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .nexora-e-icon{background:var(--nx-dark-surface-2)}';
    $css .= '[data-nexora-theme="dark"] .nexora-news-date{background:var(--nx-dark-surface-2);border-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .nexora-news-date strong,[data-nexora-theme="dark"] .nexora-news-copy small{color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .nexora-partner-form label{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .nexora-partner-form input,[data-nexora-theme="dark"] .nexora-partner-form select,'
          . '[data-nexora-theme="dark"] .nexora-partner-form textarea,[data-nexora-theme="dark"] .nexora-404 .search-field,'
          . '[data-nexora-theme="dark"] .mobile-portal-search input'
          . '{background:var(--nx-dark-surface-2);border-color:var(--nx-dark-border);color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .button-outline{border-color:var(--nx-dark-border);background:var(--nx-dark-surface-2);color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .button-light{background:var(--nx-dark-accent);color:#0b1512}';

    /* Companion plugin resource library (front.css hard-codes light card colours) */
    $css .= '[data-nexora-theme="dark"] .apr-card,[data-nexora-theme="dark"] .apr-empty,[data-nexora-theme="dark"] .apr-release,'
          . '[data-nexora-theme="dark"] .apr-compare-scroll,[data-nexora-theme="dark"] .apr-btn'
          . '{background:var(--nx-dark-surface);border-color:var(--nx-dark-border);color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .apr-chip{border-color:var(--nx-dark-border);background:var(--nx-dark-surface);color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .apr-card p,[data-nexora-theme="dark"] .apr-count,[data-nexora-theme="dark"] .apr-note,'
          . '[data-nexora-theme="dark"] .apr-release-notes,[data-nexora-theme="dark"] .apr-card-foot,[data-nexora-theme="dark"] .apr-card-meta,'
          . '[data-nexora-theme="dark"] .apr-compare th,[data-nexora-theme="dark"] .apr-compare td,[data-nexora-theme="dark"] .apr-specs dt'
          . '{color:var(--nx-dark-muted)}';
    $css .= '[data-nexora-theme="dark"] .apr-card-foot,[data-nexora-theme="dark"] .apr-compare th,[data-nexora-theme="dark"] .apr-compare td,'
          . '[data-nexora-theme="dark"] .apr-enquiry li,[data-nexora-theme="dark"] .apr-product-docs li,[data-nexora-theme="dark"] .apr-specs dl'
          . '{border-color:var(--nx-dark-border)}';
    $css .= '[data-nexora-theme="dark"] .apr-compare thead th,[data-nexora-theme="dark"] .apr-specs dl>div:nth-child(odd){background:var(--nx-dark-surface-2)}';
    $css .= '[data-nexora-theme="dark"] .apr-specs dl{background:var(--nx-dark-surface)}';
    $css .= '[data-nexora-theme="dark"] .apr-ext,[data-nexora-theme="dark"] .apr-chips-static li,[data-nexora-theme="dark"] .apr-badges li,'
          . '[data-nexora-theme="dark"] .apr-btn.is-on{background:var(--nx-dark-surface-2);color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .apr-gate{background:#2a2418;border-color:#3f3620;color:var(--nx-dark-text)}';
    $css .= '[data-nexora-theme="dark"] .apr-card-meta,[data-nexora-theme="dark"] .apr-download,[data-nexora-theme="dark"] .apr-pager a,'
          . '[data-nexora-theme="dark"] .apr-product-docs a,[data-nexora-theme="dark"] .apr-release-actions a,[data-nexora-theme="dark"] .apr-fw-line a'
          . '{color:var(--nx-dark-accent)}';
    $css .= '[data-nexora-theme="dark"] .apr-kind-feature{background:#1d3a2c;color:#9fd6b4}';
    $css .= '[data-nexora-theme="dark"] .apr-kind-fix{background:#3a3119;color:#e6cf94}';
    $css .= '[data-nexora-theme="dark"] .apr-kind-security{background:#3d2420;color:#f0b3a7}';
    $css .= '[data-nexora-theme="dark"] .apr-lock{background:#3a3119;color:#e6cf94}';
    $css .= '[data-nexora-theme="dark"] .apr-form-message,[data-nexora-theme="dark"] .nexora-form-message.is-success{background:#1c3a29;color:#a7d8ba}';
    $css .= '[data-nexora-theme="dark"] .nexora-form-message.is-error{background:#3a2320;color:#eeb4a8}';

    return $css;
}

/** Fallback rules that keep the theme's own light colours from fighting the dark palette. */
function nexora_trade_dark_extra_styles() {
    return '[data-nexora-theme="dark"] .custom-logo-dark{display:none}'
         . 'html[data-nexora-theme="dark"] .custom-logo-link.has-light-logo .custom-logo-light{display:none}'
         . 'html[data-nexora-theme="dark"] .custom-logo-link.has-light-logo .custom-logo-dark{display:block}';
}

/** Register the dark-mode styles alongside the theme stylesheet. */
function nexora_trade_dark_styles() {
    if ( ! nexora_trade_dark_enabled() ) { return; }
    $css = nexora_trade_dark_css() . nexora_trade_dark_extra_styles();
    if ( nexora_trade_dark_toggle_enabled() ) { $css .= nexora_trade_dark_toggle_styles(); }
    // The demo stylesheet loads after style.css and hard-codes light colours, so the
    // dark rules ride along with it to win on source order as well as specificity.
    $handle = wp_style_is( 'nexora-trade-demo', 'enqueued' ) ? 'nexora-trade-demo' : 'nexora-trade-style';
    wp_add_inline_style( $handle, $css );
}
add_action( 'wp_enqueue_scripts', 'nexora_trade_dark_styles', 21 );

/** Run before first paint: apply the saved or system preference so there is no flash. */
function nexora_trade_dark_head_script() {
    if ( ! nexora_trade_dark_enabled() ) { return; }
    $default = nexora_trade_dark_default_mode();
    $script  = '(function(){try{var d=document.documentElement,s=localStorage.getItem("nexora-theme"),v="' . esc_js( $default ) . '";'
             . 'if(s!=="light"&&s!=="dark"){s=(v==="dark"||(v==="system"&&window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches))?"dark":"light"}'
             . 'd.setAttribute("data-nexora-theme",s)}catch(e){}})();';
    echo '<script id="nexora-theme-init">' . $script . '</script>' . "\n";
}
add_action( 'wp_head', 'nexora_trade_dark_head_script', 1 );

/** Footer script: React to the toggle, remember the choice and follow the OS while unset. */
function nexora_trade_dark_footer_script() {
    if ( ! nexora_trade_dark_enabled() ) { return; }
    ?>
<script id="nexora-theme-toggle-js">
(function () {
  var root = document.documentElement;
  var query = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

  function current() { return root.getAttribute('data-nexora-theme') === 'dark' ? 'dark' : 'light'; }
  function apply(mode) {
    root.setAttribute('data-nexora-theme', mode);
    var buttons = document.querySelectorAll('[data-theme-toggle]');
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].setAttribute('aria-pressed', String(mode === 'dark'));
      var label = (mode === 'dark' ? buttons[i].getAttribute('data-label-light') : buttons[i].getAttribute('data-label-dark'));
      if (label) { buttons[i].setAttribute('aria-label', label); buttons[i].setAttribute('title', label); }
    }
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) { meta.setAttribute('content', mode === 'dark' ? '#131d18' : '#f5f8f5'); }
  }
  function stored() {
    try { var v = localStorage.getItem('nexora-theme'); return (v === 'light' || v === 'dark') ? v : null; } catch (e) { return null; }
  }

  document.addEventListener('click', function (event) {
    var button = event.target.closest ? event.target.closest('[data-theme-toggle]') : null;
    if (!button) { return; }
    event.preventDefault();
    var next = current() === 'dark' ? 'light' : 'dark';
    try { localStorage.setItem('nexora-theme', next); } catch (e) {}
    apply(next);
  });

  if (query) {
    var onChange = function (event) {
      if (!stored()) { apply(event.matches ? 'dark' : 'light'); }
    };
    if (query.addEventListener) { query.addEventListener('change', onChange); }
    else if (query.addListener) { query.addListener(onChange); }
  }

  apply(current());
})();
</script>
<?php
}
add_action( 'wp_footer', 'nexora_trade_dark_footer_script', 20 );

/** Toolbar / browser UI colour follows the palette. */
function nexora_trade_theme_color_meta() {
    if ( ! nexora_trade_dark_enabled() ) { return; }
    $light = nexora_colour( 'nexora_page_background', '#f5f8f5' );
    echo '<meta name="theme-color" content="' . esc_attr( $light ) . '">' . "\n";
}
add_action( 'wp_head', 'nexora_trade_theme_color_meta', 2 );
