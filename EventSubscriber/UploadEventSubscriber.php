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
use League\Flysystem\MountManager;
use Max107\Bundle\UploadBundle\Upload\Metadata\MetadataReader;
use Max107\Bundle\UploadBundle\Upload\Uploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class UploadEventSubscriber implements EventSubscriber
{
    protected Uploader $uploader;
    protected MountManager $mountManager;
    protected MetadataReader $metadata;
    protected PropertyAccessor $propertyAccessor;

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

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->isUploadable($entity)) {
            $this->doProcessUpload($args, $entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->isUploadable($entity)) {
            $this->doProcessUpload($args, $entity);
        }
    }

    /**
     * @param LifecycleEventArgs|PreUpdateEventArgs $args
     * @param mixed $entity
     */
    private function doProcessUpload(LifecycleEventArgs $args, $entity)
    {
        foreach ($this->getUploadableFields($entity) as $key => $mapping) {
            if ($args instanceof PreUpdateEventArgs && $args->hasChangedField($mapping['path'])) {
                $oldValue = $args->getOldValue($mapping['path']);
                $path = sprintf('%s://%s', $mapping['filesystem'], $oldValue);
                if ($this->mountManager->fileExists($path)) {
                    $this->mountManager->delete($path);
                }
            }

            /** @var File|UploadedFile $file */
            if ($file = $this->propertyAccessor->getValue($entity, $key)) {
                $originalName = $file instanceof UploadedFile ?
                    $file->getClientOriginalName() :
                    $file->getFilename();
                $mimeType = $file instanceof UploadedFile ?
                    $file->getClientMimeType() :
                    $file->getMimeType();

                $set = [
                    'path'         => $this->uploader->upload($this->mountManager, $file, $mapping['filesystem']),
                    'originalName' => $originalName,
                    'size'         => $file->getSize(),
                    'mimeType'     => $mimeType,
                ];
                foreach ($set as $setKey => $value) {
                    if (false === empty($mapping[$setKey])) {
                        $this->propertyAccessor->setValue($entity, $mapping[$setKey], $value);
                    }
                }
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->isUploadable($entity)) {
            foreach ($this->getUploadableFields($entity) as $mapping) {
                $file = $this->propertyAccessor->getValue($entity, $mapping['path']);
                if ($file) {
                    $path = sprintf('%s://%s', $mapping['filesystem'], $file);
                    if ($this->mountManager->fileExists($path)) {
                        $this->mountManager->delete($path);
                    }
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
        return $this->metadata->isUploadable(ClassUtils::getClass($object));
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

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
            'preRemove',
        ];
    }
}
