<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Annotation;

use Max107\Bundle\UploadBundle\Upload\Annotation\UploadableField;
use PHPUnit\Framework\TestCase;

/**
 * UploadableFieldTest.
 *
 * @author Dustin Dobervich <ddobervich@gmail.com>
 */
class UploadableFieldTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The "filesystem" attribute of UploadableField is required.
     */
    public function testExceptionThrownWhenNoMappingAttribute(): void
    {
        new UploadableField([
            'name' => 'fileName',
        ]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown key "foo" for annotation "@Max107\Bundle\UploadBundle\Upload\Annotation\UploadableField
     */
    public function testExceptionUnknownProperty(): void
    {
        new UploadableField([
            'path' => 'default',
            'filesystem' => 'default',
            'foo' => 'bar',
        ]);
    }
}
