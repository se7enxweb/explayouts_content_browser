# explayouts_content_browser

Content browser backend for Exponential Layouts on Exponential Legacy / Exponential 6. It provides a simple tree browser over the content node tree and a normalized item value object. It is used by the Exponential Layouts admin UI, the sibling content browser extensions and the collection query extensions to let editors pick content items for collections and blocks.

Exponential Legacy port inspired by `netgen/content-browser`.

## Key classes

| Class | File | Purpose |
|-------|------|---------|
| `expLayoutsContentBrowser` | `classes/explayoutscontentbrowser.php` | Browse, count and load items from the content node tree |
| `expLayoutsContentBrowserItem` | `classes/explayoutscontentbrowseritem.php` | Normalized item value object built from an `eZContentObjectTreeNode` |
| `expLayoutsContentBrowserProvider` | `classes/explayoutscontentbrowserprovider.php` | Named provider tabs (content, images, files, media, users) with class filters |

## Quick example

```php
<?php
$browser = new expLayoutsContentBrowser();

// List 25 items under node 43, optionally filtered by a search string
$items = $browser->listItems( 43, 'news', 0, 25 );

// Count items under node 43
$count = $browser->countItems( 43 );

// Load a single item by node ID
$item = $browser->loadItem( 123 );
?>
```

`expLayoutsContentBrowserItem` exposes public properties (`id`, `nodeId`, `objectId`, `name`, `classIdentifier`, `className`, `isContainer`, `isMainNode`, `published`, `modified`, `ownerId`, `ownerName`, `sectionId`, `path`, `urlAlias`) and a `toArray()` method with snake_case keys for templates and JSON output.

## Documentation

- [INSTALL.md](INSTALL.md) — activation steps and dependencies
- [doc/USAGE.md](doc/USAGE.md) — full API, usage scenarios and customization
- [doc/FAQ.md](doc/FAQ.md) — frequently asked questions
- [doc/TODO.md](doc/TODO.md) — known gaps and planned work
- [doc/SUPPORT.md](doc/SUPPORT.md) — how to get help
