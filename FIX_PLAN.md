# Fix Plan: Aqara Trade Partner Portal Theme + Plugin

## Current State Analysis

### What Works
- PHP files exist with complete functionality (customizer, shortcodes, product fields, etc.)
- Plugin code is complete (post types, meta boxes, access control, shortcodes, admin)
- CSS exists (`style.css`) with full styling
- JavaScript exists (`front.js`, `navigation.js`, `compatibility.js`)

### What's Broken
1. **Theme `functions.php` references `/inc/` files that don't exist** - files are at root
2. **Plugin `aqara-partner-resources.php` references `/includes/` files that don't exist** - files are at root
3. **Missing `/assets/` directory** - theme and plugin reference assets that don't exist
4. **Missing `/languages/` directory** - theme calls load_theme_textdomain but dir missing
5. **Next.js files mixed in** - visual mockups that don't affect WordPress but clutter the repo

---

## Phase 1: Fix Theme Structure (CRITICAL)

### 1.1 Create `/inc/` directory
```bash
mkdir -p inc
```

### 1.2 Move theme PHP files into `/inc/`
These files exist at root but should be in `/inc/`:
- `compat.php` → `inc/compat.php`
- `customizer.php` → `inc/customizer.php`
- `theme-options.php` → `inc/theme-options.php`
- `portal-shortcodes.php` → `inc/portal-shortcodes.php`
- `b2b.php` → `inc/b2b.php`
- `elementor-demo-layouts.php` → `inc/elementor-demo-layouts.php`
- `demo-import.php` → `inc/demo-import.php`

### 1.3 Create `/assets/` directory for theme
```bash
mkdir -p assets
```

Assets the theme expects (create placeholders or note they need to be added):
- `elementor-demo.css` - referenced in functions.php
- `navigation.js` - referenced in functions.php
- `compatibility.js` - referenced in functions.php
- `portal-hero.jpg` - hero image for front page
- `product-hub.jpg`, `product-panel.jpg`, `product-module.jpg` - product images
- `installation-guide.jpg` - referenced in demo import

### 1.4 Create `/languages/` directory
```bash
mkdir -p languages
```
Can be empty initially - WordPress will handle missing language files gracefully.

---

## Phase 2: Fix Plugin Structure (CRITICAL)

The plugin file `aqara-partner-resources.php` is at root but should be in its own directory structure.

### 2.1 Create plugin directory structure
```bash
mkdir -p aqara-partner-resources/includes
mkdir -p aqara-partner-resources/assets
```

### 2.2 Restructure plugin files
The plugin needs to be restructured as a proper plugin:

**Option A: Keep plugin at root but fix includes path**
- Create `aqara-partner-resources/includes/` directory
- Move plugin PHP files into `includes/`:
  - `post-types.php`
  - `meta.php`
  - `access.php`
  - `shortcodes.php`
  - `product-fields.php`
  - `compare.php`
  - `admin.php`
- Update `aqara-partner-resources.php` to reference correct paths

**Option B: Move entire plugin into subdirectory**
- Keep `aqara-partner-resources.php` as the main plugin file
- Create `includes/` next to it
- Move the 7 PHP files into `includes/`

### 2.3 Create plugin assets
```bash
# In aqara-partner-resources/assets/
- front.css  (placeholder - plugin enqueues this)
- front.js   (placeholder - plugin enqueues this)
```

The `front.js` can be similar to the existing `front.js` at root (compare/enquiry functionality).

---

## Phase 3: Handle Next.js Mockup Files

Since these are just visual representations:

### 3.1 Keep them but organize
Create a `/preview/` or `/mockup/` directory to contain the Next.js files:
```bash
mkdir -p preview
```

Move Next.js files into preview directory:
- `next.config.ts`
- `layout.tsx`
- `page.tsx`
- `page (5-11).tsx`
- `site-header.tsx`
- `site-footer.tsx`
- `catalogue.tsx`
- `portal-parts.tsx`
- `compare.tsx`
- `theme-preview.tsx`
- `training-checklist.tsx`
- `print-button.tsx`
- `copy-button.tsx`
- `not-found.tsx`
- `front.css` (the React version)
- `globals.css`
- `index.ts`
- `schema.ts`
- `route.ts`
- `route (1-4).ts`
- `content.ts`
- `b2b.ts`
- `tsconfig.json`
- `postcss.config.mjs`
- `eslint.config.mjs`
- `package.json`
- `drizzle.config.json`

### 3.2 Add a README in preview/ explaining these are mockups
```markdown
# Preview Mockup

This directory contains a Next.js application that visually represents
what the WordPress theme should look like. It is NOT a running application
and is not connected to the WordPress site.

These files are for visual reference only.
```

---

## Phase 4: Update File References

### 4.1 Update `functions.php`
After moving files to `/inc/`, the requires should work:
```php
require_once get_template_directory() . '/inc/compat.php';
require_once get_template_directory() . '/inc/customizer.php';
// etc.
```
These are already correct in the current code - we just need the files to actually be there.

### 4.3 Update plugin main file
The plugin's `require_once` statements need to point to the correct paths.

---

## Phase 5: Verify & Test

### 5.1 Theme verification
- [ ] Theme activates without errors
- [ ] Customizer options load
- [ ] Header/footer render correctly
- [ ] Front page displays (with or without Elementor)
- [ ] Shortcodes work: `[nexora_latest_news]`, `[nexora_products]`, `[nexora_partner_support]`
- [ ] B2B shortcodes work: `[aqara_faq]`, `[aqara_firmware]`

### 5.2 Plugin verification
- [ ] Plugin activates without errors
- [ ] Post types register (Resources, Firmware)
- [ ] Taxonomies register (Resource type, Audience, Product tag)
- [ ] "Trade Partner" role is created
- [ ] Shortcodes work: `[apr_resources]`, `[apr_firmware]`, `[apr_compare]`, etc.
- [ ] Product meta box appears on WooCommerce products
- [ ] Admin help page loads

### 5.3 Assets verification
- [ ] Theme assets load (CSS, JS)
- [ ] Plugin assets load (CSS, JS)
- [ ] Images display correctly

---

## File-by-File Action List

### Create directories:
```
inc/
assets/
languages/
aqara-partner-resources/includes/
aqara-partner-resources/assets/
preview/  (for Next.js mockups)
```

### Move theme PHP files to inc/:
1. compat.php
2. customizer.php
3. theme-options.php
4. portal-shortcodes.php
5. b2b.php
6. elementor-demo-layouts.php
7. demo-import.php

### Move plugin PHP files to aqara-partner-resources/includes/:
1. post-types.php (currently doesn't exist - needs creation from existing code)
2. meta.php (currently doesn't exist - needs creation from existing code)
3. access.php (currently doesn't exist - needs creation from existing code)
4. shortcodes.php (currently doesn't exist - needs creation from existing code)
5. product-fields.php (currently doesn't exist - needs creation from existing code)
6. compare.php (currently doesn't exist - needs creation from existing code)
7. admin.php (currently doesn't exist - needs creation from existing code)

### Create placeholder assets:
- assets/elementor-demo.css (copy from elementor-demo.css at root)
- assets/navigation.js (copy from navigation.js at root)
- assets/compatibility.js (copy from compatibility.js at root)
- aqara-partner-resources/assets/front.css (placeholder)
- aqara-partner-resources/assets/front.js (copy from front.js at root)

### Move Next.js mockups to preview/:
All .tsx, .ts, next.config.ts, package.json, etc.

---

## Summary

This is primarily a **file organization and directory structure fix**. The PHP code itself looks complete and functional - it just needs to be in the right places for WordPress to find it.

The Next.js files can remain as visual reference but should be separated from the WordPress theme files to avoid confusion.
