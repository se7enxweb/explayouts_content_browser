<?php
class expLayoutsContentBrowser
{
    public function listItems( $parentNodeId, $search = '', $offset = 0, $limit = 25 )
    {
        $search = trim( $search );
        $params = array(
            'Limit' => (int)$limit,
            'Offset' => (int)$offset,
            'SortBy' => array( 'name', true ),
        );

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, (int)$parentNodeId );
        if ( !is_array( $nodes ) )
            return array();

        $items = array();
        foreach ( $nodes as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;

            $item = new expLayoutsContentBrowserItem( $node );
            if ( $search !== '' && stripos( $item->name, $search ) === false )
                continue;

            $items[] = $item;
        }

        return $items;
    }

    public function countItems( $parentNodeId, $search = '' )
    {
        $search = trim( $search );
        if ( $search === '' )
            return (int)eZContentObjectTreeNode::subTreeCountByNodeID( array(), (int)$parentNodeId );

        $items = $this->listItems( (int)$parentNodeId, $search, 0, 1000 );
        return count( $items );
    }

    public function loadItem( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            return false;

        return new expLayoutsContentBrowserItem( $node );
    }

    public function listItemsAsArray( $parentNodeId, $search = '', $offset = 0, $limit = 25 )
    {
        $items = $this->listItems( (int)$parentNodeId, $search, (int)$offset, (int)$limit );
        $result = array();
        foreach ( $items as $item )
            $result[] = $item->toArray();
        return $result;
    }
}
