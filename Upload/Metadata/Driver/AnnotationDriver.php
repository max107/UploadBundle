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
use Max107\Bundle\UploadBundle\Upload\Metadata\ClassMetadata;
use Metadata\Driver\AdvancedDriverInterface;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
class AnnotationDriver implements AdvancedDriverInterface
{
    /**
     * @deprecated
     */
    const UPLOADABLE_ANNOTATION = Uploadable::class;

    /**
     * @deprecated
     */
    const UPLOADABLE_FIELD_ANNOTATION = UploadableField::class;

    protected $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!$this->isUploadable($class)) {
            return;
        }

        $classMetadata = new ClassMetadata($class->name);
        $classMetadata->fileResources[] = $class->getFileName();

        foreach ($class->getProperties() as $property) {
            $uploadableField = $this->reader->getPropertyAnnotation($property, UploadableField::class);
            if (null === $uploadableField) {
                continue;
            }
            /* @var $uploadableField UploadableField */
            //TODO: try automatically determinate target fields if embeddable used

            $fieldMetadata = [
                'filesystem' => $uploadableField->getFilesystem(),
                'propertyName' => $property->getName(),
                'name' => $uploadableField->getName(),
                'path' => $uploadableField->getPath(),
                'size' => $uploadableField->getSize(),
                'mimeType' => $uploadableField->getMimeType(),
                'originalName' => $uploadableField->getOriginalName(),
                'dimensions' => $uploadableField->getDimensions(),
            ];

            //TODO: store UploadableField object instead of array
            $classMetadata->fields[$property->getName()] = $fieldMetadata;
        }

        return $classMetadata;
    }

    public function getAllClassNames()
    {
        return [];
    }

    protected function isUploadable(\ReflectionClass $class)
    {
        return null !== $this->reader->getClassAnnotation($class, Uploadable::class);
    }
}
