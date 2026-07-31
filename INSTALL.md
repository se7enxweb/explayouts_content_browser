# Installing explayouts_content_browser

## Requirements

- Exponential Legacy / Exponential 6
- PHP 8.1, 8.2, 8.3 or 8.4

## Dependencies

None. This extension is self-contained; it is itself a dependency of `explayouts_content_browser_core`, `explayouts_content_browser_ui`, `explayouts_relation_list_query`, `explayouts_tags_query` and `explayouts_site_api`.

## Steps

1. Place the extension in `extension/explayouts_content_browser`.

2. Activate it in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=explayouts_content_browser
   ```

   To activate it for a single siteaccess only, use `ActiveAccessExtensions[]` in `settings/siteaccess/<name>/site.ini.append.php` instead.

3. Regenerate autoloads:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

4. Clear caches:

   ```bash
   php bin/php/ezcache.php --clear-all --purge --allow-root-user
   ```
