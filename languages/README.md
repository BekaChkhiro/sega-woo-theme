# Translation Files for Sega WooCommerce Theme

## Overview

This theme supports multilingual functionality via WPML plugin. The text domain is `sega-woo-theme`.

## Files

| File | Description |
|------|-------------|
| `sega-woo-theme.pot` | Translation template (444 strings) |
| `ka_GE.po` / `ka_GE.mo` | Georgian translations |
| `ru_RU.po` / `ru_RU.mo` | Russian translations |

## Supported Languages

- **Georgian (GE)** - `ka_GE`
- **Russian (RU)** - `ru_RU`
- **English (EN)** - Default language (no translation file needed)

## How Translations Work

1. **Theme Textdomain**: All translatable strings use `__('String', 'sega-woo-theme')` or `_e('String', 'sega-woo-theme')`
2. **Loading**: Translations are loaded in `app/setup.php` via `load_theme_textdomain()`
3. **WPML Config**: `wpml-config.xml` in theme root defines translatable admin texts and settings

## Testing WPML Integration

### Prerequisites
1. Install and activate WPML Multilingual CMS plugin
2. Install and activate WPML String Translation plugin
3. Install and activate WooCommerce Multilingual plugin (for product translations)

### Test Steps

1. **Verify Language Switcher**
   - Check header displays GE/RU/EN language codes
   - Click to switch between languages
   - Verify dropdown shows all languages with native names

2. **Test Theme String Translations**
   - Go to WPML > String Translation
   - Filter by domain: `sega-woo-theme`
   - Verify theme strings are visible and translatable
   - Translate a few test strings
   - Switch language and verify translations appear

3. **Test Navigation Menus**
   - Go to Appearance > Menus
   - WPML should show language sync options
   - Create menu items per language

4. **Test Product Translations**
   - Edit a product
   - Use WPML to create translations
   - Verify translated products appear when switching language

5. **Test Checkout Fields**
   - The checkout field labels in Customizer are translatable via WPML
   - Defined in `wpml-config.xml` under `admin-texts`

6. **Test Homepage Slider**
   - Slider data is translatable per language
   - Each language can have different slider images/links

## Adding New Languages

1. Copy `sega-woo-theme.pot` to `{locale}.po` (e.g., `de_DE.po` for German)
2. Translate strings in the `.po` file
3. Compile to `.mo` using: `msgfmt {locale}.po -o {locale}.mo`

## Translating New Strings

When adding new translatable strings to the theme:

1. Use `__('string', 'sega-woo-theme')` for strings that return value
2. Use `_e('string', 'sega-woo-theme')` for strings that echo directly
3. Use `esc_html__()` or `esc_attr__()` for escaped output
4. Regenerate POT file or manually add to existing `.po` files

## WPML Configuration

The `wpml-config.xml` file defines:

- **admin-texts**: Theme mods that are translatable (slider data, checkout field labels)
- **custom-types**: Post types to translate (products, variations)
- **taxonomies**: Product categories, tags, attributes
- **custom-fields**: Product meta fields to translate or copy
- **shortcodes**: Shortcode attributes with translatable IDs

## Troubleshooting

1. **Translations not appearing**: Clear view cache with `wp acorn view:clear`
2. **WPML not detecting strings**: Ensure text domain matches in all files
3. **Language switcher not showing**: Check if WPML is active and configured
4. **MO files not loading**: Verify file permissions and locale names match WordPress

## Locale Codes Reference

| Language | WPML Code | WordPress Locale | Display Code |
|----------|-----------|-----------------|--------------|
| Georgian | `ka-ge` | `ka_GE` | GE |
| Russian | `ru` | `ru_RU` | RU |
| English | `en` | `en_US` | EN |

**Note:** WPML uses its own language codes (e.g., `ka-ge`) which differ from WordPress locales (e.g., `ka_GE`). The theme's LanguageSwitcher component handles both formats.
