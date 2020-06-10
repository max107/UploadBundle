<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Max107\Bundle\UploadBundle\Upload\Metadata\MetadataReader;
use Max107\Bundle\UploadBundle\Upload\Uploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class UploadEventSubscriber implements EventSubscriber
{
    /**
     * @var Uploader
     */
    protected $uploader;
    /**
     * @var MountManager
     */
    protected $mountManager;
    /**
     * @var MetadataReader
     */
    protected $metadata;
    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param MetadataReader $metadata
     * @param Uploader $uploader
     * @param MountManager $mountManager
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        MetadataReader $metadata,
        Uploader $uploader,
        MountManager $mountManager,
        PropertyAccessor $propertyAccessor
    ) {
        $this->metadata = $metadata;
        $this->uploader = $uploader;
        $this->mountManager = $mountManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $this->isUploadable($entity)) {
            return;
        }

        $this->doProcessUpload($args, $entity);
    }

    /**
     * @param PreUpdateEventArgs $args
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $this->isUploadable($entity)) {
            return;
        }

        $this->doProcessUpload($args, $entity);
    }

    /**
     * @param string $prefix
     * @return FilesystemInterface
     */
    protected function getFilesystem(string $prefix): FilesystemInterface
    {
        return $this->mountManager->getFilesystem($prefix);
    }

    /**
     * @param LifecycleEventArgs|PreUpdateEventArgs $args
     * @param mixed $entity
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    private function doProcessUpload(LifecycleEventArgs $args, $entity)
    {
        foreach ($this->getUploadableFields($entity) as $key => $mapping) {
            $filesystem = $this->getFilesystem($mapping['filesystem']);

            if ($args instanceof PreUpdateEventArgs && $args->hasChangedField($mapping['path'])) {
                $oldValue = $args->getOldValue($mapping['path']);
                if ($filesystem->has($oldValue)) {
                    $filesystem->delete($oldValue);
                }
            }

            /** @var File|UploadedFile $file */
            if ($file = $this->propertyAccessor->getValue($entity, $key)) {
                $set = [
                    'path' => $this->uploader->upload($filesystem, $file),
                    'originalName' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mimeType' => $file->getClientMimeType(),
                ];
                foreach ($set as $setKey => $value) {
                    if (false === empty($mapping[$setKey])) {
                        $this->propertyAccessor->setValue($entity, $mapping[$setKey], $value);
                    }
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $this->isUploadable($entity)) {
            return;
        }

        foreach ($this->getUploadableFields($entity) as $key => $mapping) {
            $file = $this->propertyAccessor->getValue($entity, $mapping['path']);
            if ($file) {
                $filesystem = $this->getFilesystem($mapping['filesystem']);
                if ($filesystem->has($file)) {
                    $filesystem->delete($file);
                }
            }
        }
    }

    /**
     * Checks if the given object is uploadable using the current mapping.
     *
     * @param mixed $object The object to test
     * @return bool
     */
    protected function isUploadable($object): bool
    {
        return $this->metadata->isUploadable(
            ClassUtils::getClass($object)
        );
    }

    /**
     * Returns a list of uploadable fields for the given object and mapping.
     *
     * @param mixed $object
     * @return array
     */
    protected function getUploadableFields($object): array
    {
        return $this->metadata->getUploadableFields(ClassUtils::getClass($object));
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
            'preRemove',
        ];
    }
}
