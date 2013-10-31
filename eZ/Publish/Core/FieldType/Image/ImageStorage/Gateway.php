<?php
/**
 * File containing the ImageStorage Gateway
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\Image\ImageStorage;

use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\Core\FieldType\StorageGateway;

abstract class Gateway extends StorageGateway
{
    /**
     * Returns the node path string of $versionInfo
     *
     * @param VersionInfo $versionInfo
     *
     * @return string
     */
    abstract public function getNodePathString( VersionInfo $versionInfo );

    /**
     * Stores a reference to the image in $path for $fieldId
     *
     * @param string $path
     * @param mixed $fieldId
     *
     * @return void
     */
    abstract public function storeImageReference( $path, $fieldId );

    /**
     * Removes all references from $fieldId to a path that starts with $path
     *
     * @param string $path
     * @param int $versionNo
     * @param mixed $fieldId
     *
     * @return void
     */
    abstract public function removeImageReferences( $path, $fieldId );

    /**
     * Returns the map of image files for $fieldIds
     *
     * @param VersionInfo $versionInfo
     * @param array $fieldIds
     *
     * @return array An array of arrays of image path, indexed by field id
     */
    abstract public function getImageFiles( VersionInfo $versionInfo, array $fieldIds );

    /**
     * Tells if $imagePath for $fieldId in $versionInfo can be deleted
     *
     * An image file can usually be deleted if it isn't needed by any other attribute
     *
     * @param mixed $fieldId
     * @param VersionInfo $versionInfo
     * @param string $imagePath
     *
     * @return bool
     */
    abstract public function imageFileCanBeDeleted( $fieldId, VersionInfo $versionInfo, $imagePath );
}

