<?php
/**
 * File containing the ImageStorageTest class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\Tests\Image;

use eZ\Publish\Core\FieldType\Image\ImageStorage;
use eZ\Publish\Core\FieldType\Image\PathGenerator;
use eZ\Publish\Core\FieldType\Image\ImageStorage\Gateway;
use eZ\Publish\Core\IO\MetadataHandler;
use eZ\Publish\Core\IO\IOService;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\Core\IO\Values\BinaryFile;

class ImageStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testDeleteFieldData()
    {
        $versionInfo = new VersionInfo;
        $fieldIds = array( 1, 2, 3 );
        $firstImagePath = 'path/to/first/image.png';
        $secondImagePath = 'path/to/third/image.png';

        $this->getGatewayMock()
            ->expects( $this->once() )
            ->method( 'getImageFiles' )
            ->with( $versionInfo, $fieldIds )
            ->will(
                $this->returnValue(
                    array(
                        1 => $firstImagePath,
                        2 => $firstImagePath,
                        3 => $secondImagePath
                    )
                )
            );

        $this->getGatewayMock()
            ->expects( $this->exactly( 3 ) )
            ->method( 'imageFileCanBeDeleted' )
            ->will(
                $this->returnValueMap(
                    array(
                        array( 1, $versionInfo, $firstImagePath, false ),
                        array( 2, $versionInfo, $firstImagePath, true ),
                        array( 3, $versionInfo, $secondImagePath, false )
                    )
                )
            );

        $binaryFile = new BinaryFile( array( 'id' => $firstImagePath ) );

        $this->getIOServiceMock()
            ->expects( $this->once() )
            ->method( 'loadBinaryFile' )
            ->with( $firstImagePath )
            ->will( $this->returnValue( $binaryFile ) );

        $this->getIOServiceMock()
            ->expects( $this->once() )
            ->method( 'deleteBinaryFile' )
            ->with( $binaryFile );

        $this->getGatewayMock()
            ->expects( $this->exactly( 3 ) )
            ->method( 'removeImageReferences' )
            ->will(
                $this->returnValueMap(
                    array(
                        array( $firstImagePath, 1 ),
                        array( $firstImagePath, 2 ),
                        array( $secondImagePath, 3 )
                    )
                )
            );

        $this->getStorageHandler()->deleteFieldData(
            $versionInfo,
            $fieldIds,
            $this->getContext()
        );
    }

    protected function getContext()
    {
        return array( 'identifier' => 'test', 'connection' => true );
    }

    /**
     * @return ImageStorage
     */
    protected function getStorageHandler()
    {
        return new ImageStorage(
            array( 'test' => $this->getGatewayMock() ),
            $this->getIOServiceMock(),
            $this->getPathGeneratorMock(),
            $this->getMetadataHandlerMock()
        );
    }

    /**
     * @return IOService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getIOServiceMock()
    {
        if ( !isset( $this->IOServiceMock ) )
        {
            $this->IOServiceMock = $this->getMockBuilder( 'eZ\Publish\Core\IO\IOService' )
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $this->IOServiceMock;
    }

    /**
     * @return PathGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPathGeneratorMock()
    {
        if ( !isset( $this->pathGeneratorMock ) )
        {
            $this->pathGeneratorMock = $this->getMock( 'eZ\Publish\Core\FieldType\Image\PathGenerator' );
        }

        return $this->pathGeneratorMock;
    }

    /**
     * @return Gateway|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getGatewayMock()
    {
        if ( !isset( $this->gatewayMock) )
        {
            $this->gatewayMock = $this->getMock( 'eZ\Publish\Core\FieldType\Image\ImageStorage\Gateway' );
        }

        return $this->gatewayMock;
    }

    protected function getMetadataHandlerMock()
    {
        if ( !isset( $this->metadataHandlerMock) )
        {
            $this->metadataHandlerMock = $this->getMock( 'eZ\Publish\Core\IO\MetadataHandler' );
        }

        return $this->metadataHandlerMock;
    }

    /**
     * @var PathGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pathGeneratorMock;

    /**
     * @var IOService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $IOServiceMock;

    /**
     * @var MetadataHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataHandlerMock;
}
