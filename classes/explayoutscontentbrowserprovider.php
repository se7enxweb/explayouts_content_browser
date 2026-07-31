<?php

class expLayoutsContentBrowserProvider
{
    protected static $providerMap = array(
        'content' => array(
            'label' => 'Content',
            'class_filter_type' => 'include',
            'class_filter_array' => array(),
        ),
        'images' => array(
            'label' => 'Images',
            'class_filter_type' => 'include',
            'class_filter_array' => array( 'image' ),
        ),
        'files' => array(
            'label' => 'Files',
            'class_filter_type' => 'include',
            'class_filter_array' => array( 'file' ),
        ),
        'media' => array(
            'label' => 'Media',
            'class_filter_type' => 'include',
            'class_filter_array' => array( 'image', 'file', 'video' ),
        ),
        'users' => array(
            'label' => 'Users',
            'class_filter_type' => 'include',
            'class_filter_array' => array( 'user' ),
            'default_parent_node' => 5,
        ),
    );

    public static function getProviders()
    {
        $result = array();
        foreach ( self::$providerMap as $identifier => $config )
            $result[$identifier] = isset( $config['label'] ) ? $config['label'] : $identifier;
        return $result;
    }

    public function getItems( $providerIdentifier, $parentNodeId = 0, $search = '', $offset = 0, $limit = 25 )
    {
        $providerIdentifier = trim( $providerIdentifier );
        if ( !isset( self::$providerMap[$providerIdentifier] ) )
            return array( 'items' => array(), 'count' => 0 );

        $config = self::$providerMap[$providerIdentifier];
        $parentNodeId = (int)$parentNodeId;
        if ( $parentNodeId <= 0 && isset( $config['default_parent_node'] ) )
            $parentNodeId = (int)$config['default_parent_node'];

        $params = array(
            'Limit' => (int)$limit,
            'Offset' => (int)$offset,
            'SortBy' => array( 'name', true ),
        );

        $classFilterArray = isset( $config['class_filter_array'] ) ? $config['class_filter_array'] : array();
        if ( is_array( $classFilterArray ) && count( $classFilterArray ) > 0 )
        {
            $params['ClassFilterType'] = isset( $config['class_filter_type'] ) ? $config['class_filter_type'] : 'include';
            $params['ClassFilterArray'] = $classFilterArray;
        }

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeId );
        if ( !is_array( $nodes ) )
            $nodes = array();

        $search = trim( $search );
        $items = array();
        foreach ( $nodes as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;

            $item = new expLayoutsContentBrowserItem( $node );
            if ( $search !== '' && stripos( $item->name, $search ) === false )
                continue;

            $items[] = $item->toArray();
        }

        return array(
            'items' => $items,
            'count' => $this->countItems( $providerIdentifier, $parentNodeId, $search ),
            'offset' => (int)$offset,
            'limit' => (int)$limit,
        );
    }

    public function countItems( $providerIdentifier, $parentNodeId, $search = '' )
    {
        $providerIdentifier = trim( $providerIdentifier );
        if ( !isset( self::$providerMap[$providerIdentifier] ) )
            return 0;

        $config = self::$providerMap[$providerIdentifier];
        $parentNodeId = (int)$parentNodeId;
        if ( $parentNodeId <= 0 && isset( $config['default_parent_node'] ) )
            $parentNodeId = (int)$config['default_parent_node'];

        $params = array();
        $classFilterArray = isset( $config['class_filter_array'] ) ? $config['class_filter_array'] : array();
        if ( is_array( $classFilterArray ) && count( $classFilterArray ) > 0 )
        {
            $params['ClassFilterType'] = isset( $config['class_filter_type'] ) ? $config['class_filter_type'] : 'include';
            $params['ClassFilterArray'] = $classFilterArray;
        }

        if ( trim( $search ) === '' )
            return (int)eZContentObjectTreeNode::subTreeCountByNodeID( $params, $parentNodeId );

        $items = $this->getItems( $providerIdentifier, $parentNodeId, $search, 0, 1000 );
        return count( $items['items'] );
    }
}
