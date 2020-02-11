<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\Metadata;

use Max107\Bundle\UploadBundle\Upload\Exception\MappingNotFoundException;
use Metadata\AdvancedMetadataFactoryInterface;
use RuntimeException;

/**
 * Exposes a simple interface to read objects metadata.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class MetadataReader
{
    /**
     * @var AdvancedMetadataFactoryInterface
     */
    protected $reader;

    /**
     * @param AdvancedMetadataFactoryInterface $reader The "low-level" metadata reader
     */
    public function __construct(AdvancedMetadataFactoryInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Tells if the given class is uploadable.
     *
     * @param string $class The class name to test (FQCN)
     *
     * @return bool
     */
    public function isUploadable(string $class): bool
    {
        return null !== $this->reader->getMetadataForClass($class);
    }

    /**
     * Search for all uploadable classes.
     *
     * @throws RuntimeException
     *
     * @return array|null A list of uploadable class names
     */
    public function getUploadableClasses(): ?array
    {
        return $this->reader->getAllClassNames();
    }

    /**
     * Attempts to read the uploadable fields.
     *
     * @param string $class The class name to test (FQCN)
     * @return array A list of uploadable fields
     */
    public function getUploadableFields(string $class): array
    {
        if (null === $metadata = $this->reader->getMetadataForClass($class)) {
            throw new MappingNotFoundException($mapping ?? '', $class);
        }

        $uploadableFields = [];
        foreach ($metadata->classMetadata as $classMetadata) {
            $uploadableFields = array_merge(
                $uploadableFields,
                $classMetadata->fields
            );
        }

        return $uploadableFields;
    }

    /**
     * Attempts to read the mapping of a specified property.
     *
     * @param string $class The class name to test (FQCN)
     * @param string $field The field
     *
     * @throws MappingNotFoundException
     *
     * @return mixed The field mapping
     */
    public function getUploadableField(string $class, string $field)
    {
        $fieldsMetadata = $this->getUploadableFields($class);

        return $fieldsMetadata[$field] ?? null;
    }
}
