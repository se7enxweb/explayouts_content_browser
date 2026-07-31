# FAQ — explayouts_content_browser

## Is the search a full-text search?

No. `listItems()` fetches a page of subtree nodes and then filters them in PHP with a case-insensitive substring match (`stripos`) against the node name only. Class names and attribute contents are not searched. Use `eZSearch` or `expquery_translator` for real search.

## Why is `countItems()` wrong on very large trees when I pass a search string?

With a non-empty search string, `countItems()` fetches at most 1000 items and counts the matches, so trees with more than 1000 nodes under the parent can be under-counted. Without a search string the count comes from `eZContentObjectTreeNode::subTreeCountByNodeID()` and is exact.

## How do I add or change a provider tab?

The provider map is currently a hard-coded static array in `expLayoutsContentBrowserProvider` (`content`, `images`, `files`, `media`, `users`). Extend the class or adjust the map; making it INI-configurable is on the TODO list.

## What does `isContainer` mean on an item?

It is `true` when the node's `children_count` is greater than zero, i.e. the node already has children. It does not reflect the content class container flag.

## Which node does the `users` provider start at?

When no parent node is passed, the `users` provider defaults to node 5, the standard Users tree root. All other providers require an explicit parent node or fall back to the node you pass in.

## What format do `published` and `modified` use?

Both are pre-formatted strings in `Y-m-d H:i` format, or an empty string when the timestamp is missing.
