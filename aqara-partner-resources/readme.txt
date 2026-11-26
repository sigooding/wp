=== Aqara Partner Resources ===
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPL-2.0-or-later

Companion plugin for a B2B distributor portal. Adds a resource library, partner-only downloads,
firmware release notes, product spec fields, catalogue filters, side-by-side comparison and an
enquiry list. Deliberately contains no pricing, cart or checkout.

== Why a plugin and not the theme? ==
Content should survive a theme change. Resources, firmware notes and product fields live here;
the theme only styles them. The plugin also works with any theme (it falls back to neutral colours).

== Install ==
1. Plugins → Add New → Upload Plugin → choose the ZIP → Activate.
2. Resources → Help & Setup → "Create sample content" to see everything working.
3. Add [apr_resources] to your Downloads / Technical pages and [apr_firmware] to a Firmware page
   (Elementor: drag a Shortcode widget and paste the shortcode).
4. If /resources/ or /firmware/ shows a 404, go to Settings → Permalinks and click Save.

== Daily use ==
* Resources: upload a file (or paste an external link), set Resource type, tag the related product
  by its slug (e.g. hub-m3), choose Audience. "Partners only" = login required.
* Firmware: one post per release with version, date, type and notes.
* Products (WooCommerce): fill the "Partner portal details" box; this powers filters,
  comparison and the product page spec table. Leave prices blank.
* Partners: Users → Add New → Role "Trade Partner". That role can download partner-only files.

== Shortcodes ==
[apr_resources type="" product="" limit="12" filters="yes"]
[apr_firmware product=""]
[apr_product_resources product=""]
[apr_compare]            (place on a page with slug "compare")
[apr_enquiry_list]

== Security notes ==
Partner-only files are served through a login-checked URL and the direct file link is never shown.
For strong protection, upload them to wp-content/uploads/partner-only/ and block that folder at the
web server level (instructions in Help & Setup). Test while logged out.

== Sample content ==
Everything created by "Create sample content" is prefixed SAMPLE and can be removed in one click.
Replace with official Aqara documents, release notes and product data before launch.
