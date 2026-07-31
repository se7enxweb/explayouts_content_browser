# TODO — explayouts_content_browser

- `expLayoutsContentBrowserProvider` is missing from `autoloads/explayouts_content_browser_autoload.php`; only `expLayoutsContentBrowser` and `expLayoutsContentBrowserItem` are registered there. The class currently loads only through the global generated autoload array.
- `expLayoutsContentBrowserItem` has no eZ-style `attribute()` / `hasAttribute()` accessor. The sibling query extensions (`explayouts_relation_list_query`, `explayouts_tags_query`) call `$item->attribute( 'class_identifier' )` and similar on these items, which fails; adding the accessor here is the natural fix.
- Search in `listItems()` / `countItems()` is a post-fetch substring filter; with a search string, counting is capped at the first 1000 fetched items. Replace with a server-side filter (attribute filter or `eZSearch`).
- The provider map in `expLayoutsContentBrowserProvider` is hard-coded in the class. Move it to an INI file so sites can define their own provider tabs without patching code.
- Provider search counting reuses `getItems()` with a 1000-item cap, doubling the fetch work for counted, searched listings.
