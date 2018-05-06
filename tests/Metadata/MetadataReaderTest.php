<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Metadata;

use Max107\Bundle\UploadBundle\Upload\Metadata\MetadataReader;
use Metadata\AdvancedMetadataFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MetadataReaderTest extends TestCase
{
    /**
     * @var MetadataReader
     */
    protected $reader;

    /**
     * @var AdvancedMetadataFactoryInterface|MockObject
     */
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(AdvancedMetadataFactoryInterface::class);
        $this->reader = new MetadataReader($this->factory);
    }

    public function testIsUploadable(): void
    {
        $this
            ->factory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with('ClassName')
            ->will($this->returnValue('something not null'));

        $this->assertTrue($this->reader->isUploadable('ClassName'));
    }

    public function testIsUploadableWithGivenMapping(): void
    {
        $fields = ['field' => ['path' => 'joe']];
        $classMetadata = new \stdClass();
        $classMetadata->fields = $fields;
        $metadata = new \stdClass();
        $metadata->classMetadata = ['ClassName' => $classMetadata];

        $this
            ->factory
            ->method('getMetadataForClass')
            ->with('ClassName')
            ->will($this->returnValue($metadata));

        $this->assertTrue($this->reader->isUploadable('ClassName'));
    }

    public function testIsUploadableForNotUploadable(): void
    {
        $this->factory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with('ClassName')
            ->will($this->returnValue(null));

        $this->assertFalse($this->reader->isUploadable('ClassName'));
    }

    public function testGetUploadableClassesForwardsCallsToTheFactory(): void
    {
        $this->factory
            ->expects($this->once())
            ->method('getAllClassNames');

        $this->reader->getUploadableClasses();
    }

    public function testGetUploadableFields(): void
    {
        $fields = [
            'foo' => ['mapping' => 'foo_mapping'],
            'bar' => ['mapping' => 'bar_mapping'],
            'baz' => ['mapping' => 'baz_mapping'],
        ];
        $classMetadata = new \stdClass();
        $classMetadata->fields = $fields;
        $metadata = new \stdClass();
        $metadata->classMetadata = ['ClassName' => $classMetadata];

        $this->factory
            ->expects($this->exactly(2))
            ->method('getMetadataForClass')
            ->with('ClassName')
            ->will($this->returnValue($metadata));

        $this->assertSame($fields, $this->reader->getUploadableFields('ClassName'));

        $barFields = [
            'foo' => ['mapping' => 'foo_mapping'],
            'bar' => ['mapping' => 'bar_mapping'],
            'baz' => ['mapping' => 'baz_mapping'],
        ];
        $this->assertSame($barFields, $this->reader->getUploadableFields('ClassName'));
    }

    public function testGetUploadableFieldsWithInheritance(): void
    {
        $classMetadata = new \stdClass();
        $classMetadata->fields = ['bar', 'baz'];
        $subClassMetadata = new \stdClass();
        $subClassMetadata->fields = ['foo'];
        $metadata = new \stdClass();
        $metadata->classMetadata = [
            'ClassName' => $classMetadata,
            'SubClassName' => $subClassMetadata,
        ];

        $this->factory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with('SubClassName')
            ->will($this->returnValue($metadata));

        $this->assertSame(['bar', 'baz', 'foo'], $this->reader->getUploadableFields('SubClassName'));
    }

    /**
     * @dataProvider fieldsMetadataProvider
     *
     * @param array $fields
     * @param $expectedMetadata
     */
    public function testGetUploadableField(array $fields, $expectedMetadata): void
    {
        $classMetadata = new \stdClass();
        $classMetadata->fields = $fields;
        $metadata = new \stdClass();
        $metadata->classMetadata = ['ClassName' => $classMetadata];

        $this->factory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with('ClassName')
            ->will($this->returnValue($metadata));

        $this->assertSame($expectedMetadata, $this->reader->getUploadableField('ClassName', 'field'));
    }

    /**
     * @expectedException \Max107\Bundle\UploadBundle\Upload\Exception\MappingNotFoundException
     */
    public function testGetUploadableFieldWithInvalidClass(): void
    {
        $this->reader->getUploadableFields('InvalidClassName');
    }

    public function fieldsMetadataProvider()
    {
        return [
            [['field' => 'toto'], 'toto'],
            [['lala' => 'toto'], null],
        ];
    }
}
