<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use League\Flysystem\MountManager;
use Max107\Bundle\UploadBundle\EventSubscriber\UploadEventSubscriber;
use Max107\Bundle\UploadBundle\Upload\Metadata\MetadataReader;
use Max107\Bundle\UploadBundle\Upload\Uploader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class UploadEventSubscriberTest extends TestCase
{
    /**
     * @var MetadataReader|MockObject
     */
    protected $metadataReader;
    /**
     * @var MountManager|MockObject
     */
    protected $mountManager;
    /**
     * @var Uploader|MockObject
     */
    protected $uploader;
    /**
     * @var UploadEventSubscriber
     */
    protected $subscriber;

    protected function setUp()
    {
        $this->metadataReader = $this->createMock(MetadataReader::class);
        $this->mountManager = $this->createMock(MountManager::class);
        $this->uploader = $this->createMock(Uploader::class);

        $this->subscriber = new UploadEventSubscriber(
            $this->metadataReader,
            $this->uploader,
            $this->mountManager,
            new PropertyAccessor()
        );
    }

    public function testEvents()
    {
        $this->assertSame([
            'prePersist',
            'preUpdate',
            'preRemove',
        ], $this->subscriber->getSubscribedEvents());
    }

    public function testNeverProcessNonUploadableEntities()
    {
        $this->metadataReader
            ->method('isUploadable')
            ->willReturn(false);
        $this->metadataReader
            ->expects($this->never())
            ->method('getUploadableFields');

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getEntity')->willReturn(new \stdClass());
        $this->subscriber->prePersist($args);

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getEntity')->willReturn(new \stdClass());
        $this->subscriber->preRemove($args);

        $args = $this->createMock(PreUpdateEventArgs::class);
        $args->method('getEntity')->willReturn(new \stdClass());
        $this->subscriber->preUpdate($args);
    }
}
