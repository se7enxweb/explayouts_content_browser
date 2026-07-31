<?php
class expLayoutsContentBrowserItem
{
    public $id;
    public $nodeId;
    public $objectId;
    public $name;
    public $classIdentifier;
    public $className;
    public $isContainer;
    public $isMainNode;
    public $published;
    public $modified;
    public $ownerId;
    public $ownerName;
    public $sectionId;
    public $path;
    public $urlAlias;

    public function __construct( eZContentObjectTreeNode $node )
    {
        $this->id = (int)$node->attribute( 'node_id' );
        $this->nodeId = $this->id;
        $this->objectId = (int)$node->attribute( 'contentobject_id' );
        $this->name = (string)$node->attribute( 'name' );
        $this->path = (string)$node->attribute( 'path_string' );
        $this->urlAlias = (string)$node->attribute( 'url_alias' );
        $this->isMainNode = (int)$node->attribute( 'node_id' ) === (int)$node->attribute( 'main_node_id' );

        $object = $node->attribute( 'object' );
        if ( $object instanceof eZContentObject )
        {
            $this->classIdentifier = (string)$object->attribute( 'class_identifier' );
            $this->className = (string)$object->attribute( 'class_name' );
            $this->ownerId = (int)$object->attribute( 'owner_id' );
            $this->sectionId = (int)$object->attribute( 'section_id' );
            $this->published = $this->formatDate( $object->attribute( 'published' ) );
            $this->modified = $this->formatDate( $object->attribute( 'modified' ) );
            $this->isContainer = (int)$node->attribute( 'children_count' ) > 0;

            $this->ownerName = $this->resolveOwnerName( $this->ownerId );
        }
        else
        {
            $this->classIdentifier = '';
            $this->className = '';
            $this->ownerId = 0;
            $this->sectionId = 0;
            $this->published = '';
            $this->modified = '';
            $this->isContainer = false;
            $this->ownerName = '';
        }
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'node_id' => $this->nodeId,
            'object_id' => $this->objectId,
            'name' => $this->name,
            'class_identifier' => $this->classIdentifier,
            'class_name' => $this->className,
            'is_container' => $this->isContainer,
            'is_main_node' => $this->isMainNode,
            'published' => $this->published,
            'modified' => $this->modified,
            'owner_id' => $this->ownerId,
            'owner_name' => $this->ownerName,
            'section_id' => $this->sectionId,
            'path' => $this->path,
            'url_alias' => $this->urlAlias,
        );
    }

    protected function formatDate( $timestamp )
    {
        return is_numeric( $timestamp ) && (int)$timestamp > 0
            ? date( 'Y-m-d H:i', (int)$timestamp )
            : '';
    }

    protected function resolveOwnerName( $ownerId )
    {
        if ( $ownerId <= 0 )
            return '';

        $user = eZUser::fetch( $ownerId );
        if ( !$user instanceof eZUser )
            return '';

        return (string)$user->attribute( 'login' );
    }
}
