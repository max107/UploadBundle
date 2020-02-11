<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Max107\Bundle\UploadBundle\Upload\Annotation\Uploadable;
use Max107\Bundle\UploadBundle\Upload\Annotation\UploadableField;
use Max107\Bundle\UploadBundle\Upload\Metadata\ClassMetadata as UploadClassMetadata;
use Metadata\ClassMetadata as OriginalClassMetaData;
use Metadata\Driver\AdvancedDriverInterface;
use ReflectionClass;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
class AnnotationDriver implements AdvancedDriverInterface
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param ReflectionClass $class
     * @return OriginalClassMetaData
     */
    public function loadMetadataForClass(ReflectionClass $class): OriginalClassMetaData
    {
        $classMetadata = new UploadClassMetadata($class->name);
        if (false === $this->isUploadable($class)) {
            return $classMetadata;
        }

        $classMetadata->fileResources[] = $class->getFileName();
        foreach ($class->getProperties() as $property) {
            /* @var $uploadableField UploadableField */
            $uploadableField = $this->reader->getPropertyAnnotation($property, UploadableField::class);
            if (null === $uploadableField) {
                continue;
            }

            $fieldMetadata = [
                'filesystem'   => $uploadableField->getFilesystem(),
                'propertyName' => $property->getName(),
                'name'         => $uploadableField->getName(),
                'path'         => $uploadableField->getPath(),
                'size'         => $uploadableField->getSize(),
                'mimeType'     => $uploadableField->getMimeType(),
                'originalName' => $uploadableField->getOriginalName(),
                'dimensions'   => $uploadableField->getDimensions(),
            ];

            //TODO: store UploadableField object instead of array
            $classMetadata->fields[$property->getName()] = $fieldMetadata;
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    protected function isUploadable(ReflectionClass $class)
    {
        return null !== $this->reader->getClassAnnotation($class, Uploadable::class);
    }
}
