<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Metadata\Driver;

use Max107\Bundle\UploadBundle\Tests\Bundle\TestBundle\Entity\Article;
use Max107\Bundle\UploadBundle\Tests\DummyEntity;
use Max107\Bundle\UploadBundle\Upload\Annotation\UploadableField;
use Max107\Bundle\UploadBundle\Upload\Metadata\ClassMetadata;
use Max107\Bundle\UploadBundle\Upload\Metadata\Driver\AnnotationDriver;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * AnnotationDriverTest.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class AnnotationDriverTest extends TestCase
{
    public function testReadUploadableAnnotation(): void
    {
        $entity = new DummyEntity();

        $reader = $this->createMock('Doctrine\Common\Annotations\Reader');
        $reader
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue('something not null'));
        $reader
            ->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue(new UploadableField([
                'path'       => 'default',
                'filesystem' => 'default',
                'name'       => 'fileName',
            ])));

        $driver = new AnnotationDriver($reader);
        $metadata = $driver->loadMetadataForClass(new ReflectionClass($entity));

        $this->assertInstanceOf(ClassMetadata::class, $metadata);
        $this->assertObjectHasAttribute('fields', $metadata);
        $this->assertEquals([
            'file' => [
                'filesystem'   => 'default',
                'propertyName' => 'file',
                'name'         => 'fileName',
                'size'         => null,
                'mimeType'     => null,
                'originalName' => null,
                'dimensions'   => null,
                'path'         => 'default',
            ],
        ], $metadata->fields);
    }

    public function testReadUploadableAnnotationReturnsNullWhenNonePresent(): void
    {
        $entity = new DummyEntity();

        $reader = $this->createMock('Doctrine\Common\Annotations\Reader');
        $reader
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue(null));
        $reader
            ->expects($this->never())
            ->method('getPropertyAnnotation');

        $driver = new AnnotationDriver($reader);
        $metadata = $driver->loadMetadataForClass(new ReflectionClass($entity));

        $this->assertInstanceOf(ClassMetadata::class, $metadata);
    }

    public function testReadTwoUploadableFields(): void
    {
        $entity = new Article();

        $reader = $this->createMock('Doctrine\Common\Annotations\Reader');
        $reader
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue('something not null'));
        $reader
            ->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue(new UploadableField([
                'path'       => 'default',
                'filesystem' => 'default',
                'name'       => 'attachmentName',
            ])));
        $reader
            ->expects($this->at(3))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue(new UploadableField([
                'filesystem'   => 'default',
                'name'         => 'imageName',
                'size'         => 'sizeField',
                'mimeType'     => 'mimeTypeField',
                'originalName' => 'originalNameField',
                'dimensions'   => null,
                'path'         => 'default',
            ])));

        $driver = new AnnotationDriver($reader);
        $metadata = $driver->loadMetadataForClass(new ReflectionClass($entity));

        $this->assertEquals([
            'attachment' => [
                'filesystem'   => 'default',
                'propertyName' => 'attachment',
                'name'         => 'attachmentName',
                'size'         => null,
                'mimeType'     => null,
                'originalName' => null,
                'dimensions'   => null,
                'path'         => 'default',
            ],
            'image' => [
                'filesystem'   => 'default',
                'propertyName' => 'image',
                'name'         => 'imageName',
                'size'         => 'sizeField',
                'mimeType'     => 'mimeTypeField',
                'originalName' => 'originalNameField',
                'dimensions'   => null,
                'path'         => 'default',
            ],
        ], $metadata->fields);
    }

    public function testReadNoUploadableFieldsWhenNoneExist(): void
    {
        $entity = new DummyEntity();

        $reader = $this->createMock('Doctrine\Common\Annotations\Reader');
        $reader
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->will($this->returnValue('something not null'));

        $driver = new AnnotationDriver($reader);
        $metadata = $driver->loadMetadataForClass(new ReflectionClass($entity));

        $this->assertEmpty($metadata->fields);
    }

    public function testAllClassNames()
    {
        $reader = $this->createMock('Doctrine\Common\Annotations\Reader');
        $driver = new AnnotationDriver($reader);
        $this->assertEmpty($driver->getAllClassNames());
    }
}
