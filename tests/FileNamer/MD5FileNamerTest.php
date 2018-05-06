<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\FileNamer;

use Max107\Bundle\UploadBundle\Upload\FileNamer\MD5FileNamer;
use PHPUnit\Framework\TestCase;

class MD5FileNamerTest extends TestCase
{
    public function testHasher()
    {
        $hasher = new MD5FileNamer();
        $this->assertSame('acbd18db4cc2f85cedef654fccc4a4d8.bar', $hasher->getFileName('foo.bar'));
    }
}
