Aqara Trade Partner Portal — WordPress theme
Version 2.8.0

EDITING SHORTCUTS
- A "Trade Portal" menu appears in the WordPress admin bar (for anyone who can edit pages) with one-click links to Theme options, Demo content, Menus and "Edit homepage (Elementor)".
- Pages built with Elementor or Beaver Builder render without the theme's title/breadcrumbs on top, so you never get a duplicate heading. Plain WordPress pages keep the theme title and breadcrumbs.
- The announcements-per-page option only affects the front end, not admin list tables.

BRANDING
This build uses sample Aqara-style product names (Hub M3, sensors, switches) and a teal-green accent as placeholders. As an Aqara distributor, use only the official Aqara logo, imagery, copy and product data that you are licensed to use. Replace the site name and logo under Site Identity, upload your licensed logo, and swap the accent/deep colours in Trade Portal Options. Verify current product names, SKUs, regional model variants and imagery against Aqara's official partner materials before publishing. Do not enter pricing or customer/order data in catalogue mode.

QUICK START
1. Back up your site and try this theme on a staging site first.
2. Upload the ZIP in Appearance > Themes > Add New > Upload Theme. Activate it. For an existing installation of this theme, WordPress may offer to replace the installed version; back up before updating. This version retains your existing theme settings and old demo content.
3. Install and activate the FREE Elementor plugin (Plugins > Add New). Elementor Pro is not required.
4. Open Appearance > Portal Demo Content. Select whether to set the demo Home page and primary menu, and whether to add sample news and optional WooCommerce products. Click Create Elementor demo.
5. The installer creates eight NEW pages. It does not overwrite an existing page with the same slug. If the earlier Nexora demo exists, it is kept separately. The imported pages are listed with "Edit with Elementor" and "View page" links in the installer.
6. Open a demo page in Elementor to edit each section, column, heading, text, button, icon and image individually. Edit the hero image by selecting its section > Style > Background. Edit resource button URLs directly in Elementor.
7. Go to Appearance > Customize > Trade Portal Options. Upload the company logo under Site Identity and manage menus under Appearance > Menus.

LICENSED PLUGINS YOU ALREADY OWN
- Elementor Free vs Beaver Builder: both are page builders and store content differently, so a page built in one cannot be edited in the other. Pick one as your standard. Keep the other installed if you like, but do not build a single page with both. The included demo layouts are Elementor-native. If Beaver Builder is your choice, skip the Elementor demo and build your own layouts; the theme is a classic theme, so Beaver Builder works normally (no sidebar issues, and the "Trade Portal Full Width" template gives a clean canvas).
- Max Mega Menu: a good fit here, because this theme renders the header and menu itself. Assign Max Mega Menu to the "Primary navigation" location. The theme detects this and hides its own mobile toggle, so you do not get two hamburger buttons. The menu's colours, fonts and dropdown styling are edited in Mega Menu > Menu Themes (the plugin's own editor) — set them there to match your brand; the theme deliberately does not override them, so your Mega Menu settings always win. If you later add an Elementor Pro header, use one menu system only.
- Smart Slider 3: use it for a richer hero without Elementor Pro. Paste its shortcode (e.g. [smartslider3 slider="1"]) into Trade Portal Options > Default Homepage > "Homepage hero slider shortcode" to replace the static hero on the built-in homepage, or add an Elementor Shortcode widget to a page. Because it loads its own CSS/JS, check that the slider width and z-index look right against the sticky header, and verify performance on mobile.
- Conflict checks that matter: two page builders on one page; two menu systems in the header; two lightbox/gallery scripts on product pages; two slider scripts competing for full-width layout. Each of these is a configuration choice, not a code incompatibility.

THEME OPTIONS (Appearance > Customize > Trade Portal Options)
- Brand, Colours & Layout: accent, deep, background and text colours; content width; card corners; typography (modern webfonts or system fonts); base text size; constrained or full-width theme pages; logo height; load Google webfonts on/off (useful for privacy or offline hosting); sticky header on/off; small label beside the logo; and the optional dark-mode logo.
- Dark Mode: a light/dark switch in the header. Set the starting scheme (match the visitor's device, always light or always dark), show or hide the switch, and recolour every part of the dark palette (page, cards, raised areas, borders, text, secondary text, accent, header) plus both states of the switch button. Visitors' choices are remembered in their browser; the switch is keyboard accessible and labelled for screen readers.
- LOGOS: upload the standard logo under Site Identity, then a light or white version under Trade Portal Options > Brand, Colours & Layout > "Logo for dark mode". The header swaps to it automatically in dark mode and the footer uses it by default (switch this off under Dark Mode if you prefer). Leave it blank to use one logo everywhere.
- Header & Navigation: portal label, navigation note, support label, search placeholder, account/login/logout labels, show or hide the search field, support link and account links, support page URL, support form recipient email.
- Announcement Bar: a slim bar above the header for current news, with its own text, link and background colour. Useful for "New products launched" or "Updated documentation".
- Footer & Contact: brand description, support column text and link, footer tagline note, copyright line with {year} and {sitename} placeholders, plus phone, email, address and opening hours with a show/hide switch. Footer columns can use assigned menus instead of the defaults.
- Products & Catalogue: catalogue-only mode (hide prices and purchasing, redirect cart/checkout/WooCommerce account), product enquiry button with custom text and destination, hide related and upsell products, show or hide SKU/category meta, products per row. Turn catalogue-only mode off if you ever want WooCommerce to sell normally.
- News & Private Areas: announcements per page, show or hide dates, and a comma-separated list of page slugs that require login (for example "downloads, technical"). This is a simple gate only: for genuine partner access control use a membership plugin, protect uploaded files and test while logged out.
- Login & Account: branded login screen with your logo, colours and heading, plus where partners are sent after login.
- 404 Page & Notices: heading, text and button for missing pages, and a sample-content reminder shown only to logged-in administrators while demo content is installed.
- Default Homepage: hero image, heading, description and resource links for the built-in homepage used before you build one in Elementor. It does not override Elementor pages.

WHAT IS EDITED WHERE
- Page content and demo homepage: Edit with Elementor. The layouts are native Elementor Free sections, columns and widgets, not a single HTML block. The theme uses normal classic sections for compatibility; Elementor may offer to convert sections into Flexbox containers.
- Header, colours and menus: WordPress Customizer and Menus, supplied by the theme. The header logo defaults to 56px tall (up from 48px) and can be set from 24px to 160px under Trade Portal Options > Brand, Colours & Layout > Logo height; the header grows with it on desktop and caps the logo at 46px on small screens. Elementor Free does not provide Elementor Pro's Theme Builder; the theme header and footer are not Elementor templates.
- News: WordPress Posts. A shortcode widget displays recent published posts. Click the widget to change the shortcode or replace it.
- Products: the Products page is an editable Elementor page with a [nexora_products] shortcode widget. With WooCommerce active and published products, it shows the WooCommerce product grid; without products it shows examples. The actual WooCommerce product archive and single-product pages use theme/WooCommerce templates, not Elementor Pro templates. Maintain products in Products > All Products.
- Support: the [nexora_partner_support] shortcode widget supplies a basic form using WordPress wp_mail. Set the recipient in Trade Portal Options > Header & Navigation and configure and test SMTP/delivery on your host. You can replace the shortcode with another form plugin if desired.
- PDFs: three demonstration PDFs are imported to Media and linked to the editable buttons. They are sample documents, not official specifications. Replace them and the sample images before publishing. Trade Portal Options > Default Homepage only applies to the fallback homepage shown before an Elementor homepage is created; it does not overwrite Elementor page edits.

OPTIONAL WOOCOMMERCE
Install WooCommerce before importing to include four sample products, or use the Add example WooCommerce products button after installing it. The theme marks products non-purchasable, hides prices and add-to-cart output, and redirects normal cart, checkout and WooCommerce My Account pages. WordPress login/profile/logout are separate. Do not enter prices or sensitive sales/customer data if these must not be exposed: APIs and third-party extensions may expose stored values. Test with your plugins and block templates. The Products demo page is intentionally not assigned as the WooCommerce Shop archive, keeping it editable in Elementor Free.

DEMO REMOVAL
The Elementor demo importer tracks the new pages, files, news, products and menu it created. Remove Elementor demo permanently deletes those items, including your edits to them. Duplicate or back up anything you want to keep first. It restores the previous homepage/menu only if the site still points to the demo. It does not remove earlier demo imports or pages it did not create.

PRIVATE ACCESS AND LAUNCH
The theme alone does not restrict access. Configure a suitable membership/access-control plugin, secure uploaded files, and test while logged out before publishing private partner material. Configure WordPress email delivery, replace all placeholder content, test responsive layouts and forms, and use a child theme for code changes. The separate Next.js preview is a design demonstration and does not import its database or resources into WordPress.

== 2.8.0 ==
* Dark mode: a header switch with a dark palette for the header, cards, forms, footer, Elementor demo
  layouts and the companion plugin's resource library. No white flash on load, keyboard accessible,
  and screen-reader labelled.
* Dark mode starting scheme: match the visitor's device (default), always light or always dark.
  Visitor choices are remembered per browser.
* Dark-mode logo: upload a separate light/white logo for dark backgrounds. The header swaps
  automatically and the footer uses it by default.
* Elementor pages you build yourself keep their own widget colours; use Elementor's global colours and
  per-widget settings if you add custom backgrounds. The included demo layouts and the theme's own
  templates, shortcodes, forms and the companion plugin's library all follow dark mode automatically.
* Logo is now larger by default (56px, was 48px); the Logo height setting accepts 24-160px and the
  header grows with it. Small screens cap the logo at 46px so the header stays tidy.
* All dark-mode colours and both states of the switch button are editable in the Customizer.

== 2.6.0 ==
* Product "Ask about this product" button now pre-fills the Support form with product name and SKU.
* Support form pre-fills topic/product from apr_topic / apr_product links (works without the companion plugin).
* Products page shows the companion plugin's protocol / works-with filter bar when the plugin is active.
* Contrast fixes (breadcrumbs, text links, logout link) and WooCommerce button colour override fix.
* COMPANION PLUGIN: install "Aqara Partner Resources" for the resource library, partner-only downloads,
  firmware notes, product spec fields, comparison and enquiry list. See its readme.
