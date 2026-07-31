# Using explayouts_content_browser

## expLayoutsContentBrowser

```php
<?php
$browser = new expLayoutsContentBrowser();

// listItems( $parentNodeId, $search = '', $offset = 0, $limit = 25 )
// Lists subtree items under a node, sorted by name, optionally filtered
// by a case-insensitive substring match against the node name.
$items = $browser->listItems( 43, 'report', 0, 25 );

// countItems( $parentNodeId, $search = '' )
// Without a search string this uses subTreeCountByNodeID(); with a search
// string it filters the first 1000 items and counts the matches.
$total = $browser->countItems( 43 );

// loadItem( $nodeId )
// Returns an expLayoutsContentBrowserItem or false.
$item = $browser->loadItem( 123 );

// listItemsAsArray( $parentNodeId, $search = '', $offset = 0, $limit = 25 )
// Same as listItems() but returns toArray() hashes, ready for templates or JSON.
$rows = $browser->listItemsAsArray( 43, '', 0, 50 );
?>
```

## expLayoutsContentBrowserItem

Built from an `eZContentObjectTreeNode`. Public properties:

```php
$item->id;               // node ID
$item->nodeId;           // node ID (alias of id)
$item->objectId;         // content object ID
$item->name;             // node name
$item->classIdentifier;  // content class identifier
$item->className;        // content class name
$item->isContainer;      // true when the node has children
$item->isMainNode;       // true when node_id equals main_node_id
$item->published;        // 'Y-m-d H:i' or ''
$item->modified;         // 'Y-m-d H:i' or ''
$item->ownerId;          // owner user ID
$item->ownerName;        // owner login name
$item->sectionId;        // section ID
$item->path;             // path_string
$item->urlAlias;         // url_alias
```

`toArray()` returns the same data with snake_case keys (`node_id`, `object_id`, `class_identifier`, `is_container`, `is_main_node`, `owner_id`, `owner_name`, `section_id`, `url_alias`, ...).

## expLayoutsContentBrowserProvider

Named provider tabs with predefined class filters. Built-in providers: `content` (no filter), `images` (`image`), `files` (`file`), `media` (`image`, `file`, `video`) and `users` (`user`, default parent node 5).

```php
<?php
// List provider identifiers => labels
$providers = expLayoutsContentBrowserProvider::getProviders();

$provider = new expLayoutsContentBrowserProvider();

// getItems( $providerIdentifier, $parentNodeId = 0, $search = '', $offset = 0, $limit = 25 )
// Returns array( 'items' => array of toArray() hashes, 'count' => int,
//                'offset' => int, 'limit' => int )
$result = $provider->getItems( 'images', 43, '', 0, 25 );

// countItems( $providerIdentifier, $parentNodeId, $search = '' )
$total = $provider->countItems( 'images', 43 );
?>
```

Unknown provider identifiers return an empty result (`array( 'items' => array(), 'count' => 0 )`), never an error.

## Scenario: feeding a module view template

```php
<?php
$browser = new expLayoutsContentBrowser();
$tpl->setVariable( 'items', $browser->listItemsAsArray( 43, '', 0, 50 ) );
?>
```

```
<ul>
    {foreach $items as $item}
        <li class="{if $item.is_container}container{/if}">
            {$item.name|wash} ({$item.class_identifier|wash})
        </li>
    {/foreach}
</ul>
```

## Scenario: JSON listing endpoint

```php
<?php
$http = eZHTTPTool::instance();
$parent = (int)$http->getVariable( 'parent', 2 );
$search = trim( $http->getVariable( 'q', '' ) );
$offset = (int)$http->getVariable( 'offset', 0 );

$browser = new expLayoutsContentBrowser();
$out = array(
    'total' => $browser->countItems( $parent, $search ),
    'items' => $browser->listItemsAsArray( $parent, $search, $offset, 25 ),
);

header( 'Content-Type: application/json' );
echo json_encode( $out );
eZExecution::cleanExit();
?>
```

## Scenario: CLI usage

Run any script that uses these classes through the bootstrap runner so the content repository is available:

```bash
php bin/php/ezexec.php ai/bin/tmp/test_content_browser.php --allow-root-user
```

Inside the script, `new expLayoutsContentBrowser()` works as soon as autoloads are generated (`php bin/php/ezpgenerateautoloads.php -e`).

## Scenario: provider-tab picker

```php
<?php
$provider = new expLayoutsContentBrowserProvider();

foreach ( expLayoutsContentBrowserProvider::getProviders() as $identifier => $label )
{
    $page = $provider->getItems( $identifier, 0, '', 0, 10 );
    echo $label . ': ' . $page['count'] . " items\n";
}
?>
```

Note: only the `users` provider has a default parent node (5); pass an explicit parent node for the others, or `subTreeByNodeID()` will receive `0` and return nothing.

## Customization

### Settings layer (INI)

This extension currently ships no INI settings and reads none — behaviour is controlled entirely through method arguments (parent node, search, offset, limit) and the provider map. When INI-configurable options are added (see [TODO.md](TODO.md)), they will follow the standard configuration cascade of this stack, lowest to highest priority:

1. `settings/*.ini` — kernel defaults
2. `extension/<ext>/settings/*.ini.append.php` — extension defaults
3. `settings/siteaccess/<siteaccess>/*.ini.append.php` — siteaccess overrides
4. `extension/<ext>/settings/siteaccess/<siteaccess>/*.ini.append.php` — extension siteaccess overrides
5. `settings/override/*.ini.append.php` — global overrides (always win)

### Template layer (design overrides)

This extension ships no templates and registers no design directory. Markup that renders browser items lives in the calling extension's design (for example `explayouts_content_browser_ui`), so template customization happens there, through the normal design cascade.

### PHP layer (extension points)

- `expLayoutsContentBrowserItem` — safe to subclass; `formatDate()` and `resolveOwnerName()` are `protected` specifically so subclasses can change date formatting or owner resolution.
- `expLayoutsContentBrowser` — stateless; subclass and override individual methods (for example `loadItem()` to add permission checks). Have your factory or module construct the subclass.
- `expLayoutsContentBrowserProvider` — the provider map is a `protected static` array referenced via `self::`, so redefining it in a subclass has no effect; to add providers today, override `getProviders()`, `getItems()` and `countItems()` in a subclass (or patch the map until it becomes INI-driven).
