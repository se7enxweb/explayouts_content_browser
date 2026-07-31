Exponential Layouts Content Browser
===================================

General description
-------------------

Exponential Layouts Content Browser (`explayouts_content_browser`) is the
content browser backend for Exponential Layouts on Exponential Legacy /
Exponential 6. It provides a simple tree browser over the content node tree
and a normalized item value object, and it is the foundation the rest of the
content browser and collection query stack is built on.

It is used by the Exponential Layouts admin UI, the sibling content browser
extensions (`explayouts_content_browser_core`, `explayouts_content_browser_ui`)
and the collection query extensions (`explayouts_relation_list_query`,
`explayouts_tags_query`, `explayouts_site_api`) to let editors pick content
items for collections and blocks.

This extension is an Exponential Legacy port inspired by
`netgen/content-browser` and provides the following capabilities:

- Content tree browsing - Use this feature to list, page and count the items
  of any content subtree from PHP, module views or CLI scripts.
- Item normalization - Use this feature to turn any `eZContentObjectTreeNode`
  into a predictable, template- and JSON-friendly value object.
- Named provider tabs - Use this feature to offer editors predefined,
  class-filtered starting points (content, images, files, media, users).
- Search filtering - Use this feature to narrow listings with a
  case-insensitive substring match against node names.

Features
--------

The following features are provided by the Exponential Layouts Content
Browser extension:

- A stateless browser service, `expLayoutsContentBrowser`, that lists subtree
  items under any parent node, sorted by name, with offset/limit pagination:
  - `listItems( $parentNodeId, $search = '', $offset = 0, $limit = 25 )` —
    returns `expLayoutsContentBrowserItem[]`, optionally filtered by a
    case-insensitive substring match against the node name.
  - `countItems( $parentNodeId, $search = '' )` — exact counts via
    `eZContentObjectTreeNode::subTreeCountByNodeID()` when no search string
    is given; filtered counting (capped at the first 1000 fetched items)
    when one is.
  - `loadItem( $nodeId )` — returns a single normalized item or `false`.
  - `listItemsAsArray( ... )` — the same listing as ready-to-use `toArray()`
    hashes for templates or JSON output.
- A normalized item value object, `expLayoutsContentBrowserItem`, built from
  an `eZContentObjectTreeNode`, exposing public properties (`id`, `nodeId`,
  `objectId`, `name`, `classIdentifier`, `className`, `isContainer`,
  `isMainNode`, `published`, `modified`, `ownerId`, `ownerName`, `sectionId`,
  `path`, `urlAlias`) plus a `toArray()` method that returns the same data
  with snake_case keys (`node_id`, `object_id`, `class_identifier`,
  `is_container`, `is_main_node`, `owner_id`, `owner_name`, `section_id`,
  `url_alias`, ...) for templates and JSON output.
- Pre-formatted `published` / `modified` date strings (`Y-m-d H:i`, or an
  empty string when the timestamp is missing) so templates never have to
  format timestamps themselves.
- A provider service, `expLayoutsContentBrowserProvider`, with named provider
  tabs and predefined class filters:
  - `content` — no class filter.
  - `images` — `image`.
  - `files` — `file`.
  - `media` — `image`, `file`, `video`.
  - `users` — `user`, with a default parent node of 5 (the Users tree root).
- A forgiving provider API: unknown provider identifiers return an empty
  result (`array( 'items' => array(), 'count' => 0 )`), never an error.
- Deliberate PHP extension points: `protected` hook methods
  (`formatDate()`, `resolveOwnerName()`) so subclasses can adjust date
  formatting, owner resolution, permission checks and more.
- No hard dependencies — the extension is fully self-contained and works in
  module views, block handlers, JSON endpoints and CLI scripts alike.

Version
-------

- The current version of Exponential Layouts Content Browser is 1.0.0
- Last Major update: July 30, 2026

Copyright
---------

- Exponential Layouts Content Browser is copyright 1998 - 2026 7x
- See: [LICENSE.md](LICENSE.md) for more information on the terms of the
  copyright and license

License
-------

Exponential Layouts Content Browser is licensed under the GNU General
Public License.

The complete license agreement is included in the [LICENSE.md](LICENSE.md)
file.

Exponential Layouts Content Browser is free software: you can redistribute
it and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 2 of the License
or at your option a later version.

Exponential Layouts Content Browser is distributed in the hope that it will
be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
Exponential Layouts Content Browser under certain conditions. The GNU GPL
license is distributed with the software, see the file [LICENSE.md](LICENSE.md).

It is also available at http://www.gnu.org/licenses/gpl.txt

You should have received a copy of the GNU General Public License along with
Exponential Layouts Content Browser in [LICENSE.md](LICENSE.md). If not, see
http://www.gnu.org/licenses/.

Using Exponential Layouts Content Browser under the terms of the GNU GPL is
free (as in freedom).

For more information or questions please contact info@se7enx.com

Requirements
------------

The following requirements exist for using the Exponential Layouts Content
Browser extension:

Exponential version
- Make sure you use Exponential 6 / Exponential Legacy.

PHP version
- Make sure you have PHP 8.1, 8.2, 8.3 or 8.4.

Extension dependencies
- None. This extension is self-contained; it is itself a dependency of
  `explayouts_content_browser_core`, `explayouts_content_browser_ui`,
  `explayouts_relation_list_query`, `explayouts_tags_query` and
  `explayouts_site_api`, so activate it before any of those.

Installation
------------

Installation is the standard extension procedure: place the extension in
`extension/explayouts_content_browser`, activate it via
`ActiveExtensions[]` (or `ActiveAccessExtensions[]` for a single
siteaccess), regenerate autoloads with
`php bin/php/ezpgenerateautoloads.php -e` and clear caches with
`php bin/php/ezcache.php --clear-all --purge --allow-root-user`.

See [INSTALL.md](INSTALL.md) for the complete step-by-step installation
instructions.

Usage
-----

The extension ships three classes which together form the browsing backend:

| Class | File | Purpose |
|-------|------|---------|
| `expLayoutsContentBrowser` | `classes/explayoutscontentbrowser.php` | Browse, count and load items from the content node tree |
| `expLayoutsContentBrowserItem` | `classes/explayoutscontentbrowseritem.php` | Normalized item value object built from an `eZContentObjectTreeNode` |
| `expLayoutsContentBrowserProvider` | `classes/explayoutscontentbrowserprovider.php` | Named provider tabs (content, images, files, media, users) with class filters |

A quick example:

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

Every returned `expLayoutsContentBrowserItem` exposes public properties
(`id`, `nodeId`, `objectId`, `name`, `classIdentifier`, `className`,
`isContainer`, `isMainNode`, `published`, `modified`, `ownerId`,
`ownerName`, `sectionId`, `path`, `urlAlias`) and a `toArray()` method with
snake_case keys for templates and JSON output.

Provider tabs give editors predefined, class-filtered starting points:

```php
<?php
// List provider identifiers => labels
$providers = expLayoutsContentBrowserProvider::getProviders();

$provider = new expLayoutsContentBrowserProvider();
$result = $provider->getItems( 'images', 43, '', 0, 25 );
$total  = $provider->countItems( 'images', 43 );
?>
```

Note that only the `users` provider has a default parent node (5); pass an
explicit parent node for the other providers.

The full usage guide in [doc/USAGE.md](doc/USAGE.md) covers the complete
API, usage scenarios (module view templates, JSON listing endpoints, CLI
scripts, provider-tab pickers) and the three customization layers of this
stack: the settings layer (the INI configuration cascade), the template
layer (design overrides) and the PHP layer (which classes and `protected`
methods are safe to subclass and override).

Documentation
-------------

| Document | Description |
|----------|-------------|
| [INSTALL.md](INSTALL.md) | Requirements, dependencies and step-by-step activation instructions |
| [doc/USAGE.md](doc/USAGE.md) | Full API reference, usage scenarios and the settings/template/PHP customization layers |
| [doc/FAQ.md](doc/FAQ.md) | Frequently asked questions and answers |
| [doc/TODO.md](doc/TODO.md) | Known gaps and planned improvements |
| [doc/SUPPORT.md](doc/SUPPORT.md) | Where and how to get help |
| [LICENSE.md](LICENSE.md) | The complete GNU General Public License agreement |

Troubleshooting
---------------

Read the FAQ
- Some problems are more common than others. The most common ones are listed
  in [doc/FAQ.md](doc/FAQ.md).

Use our support systems
- If you find a bug or defect, please report it to the
  [Exponential Layouts Content Browser: Issue Tracker](https://github.com/se7enxweb/explayouts_content_browser/issues)
- For commercial support and custom development please visit
  [se7enx.com](https://se7enx.com)
