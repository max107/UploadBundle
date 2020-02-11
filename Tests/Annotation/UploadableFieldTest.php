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
    public function testExceptionThrownWhenNoFilesystemAttribute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "filesystem" attribute of UploadableField is required.');

        new UploadableField([
            'name' => 'fileName',
        ]);
    }

    public function testExceptionThrownWhenNoPathAttribute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "path" attribute of UploadableField is required.');

        new UploadableField([
            'filesystem' => 'default',
        ]);
    }

    public function testExceptionUnknownProperty(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown key "foo" for annotation "@Max107\Bundle\UploadBundle\Upload\Annotation\UploadableField');

        new UploadableField([
            'path'       => 'default',
            'filesystem' => 'default',
            'foo'        => 'bar',
        ]);
    }
}
