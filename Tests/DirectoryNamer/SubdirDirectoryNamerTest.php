<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Naming;

use Max107\Bundle\UploadBundle\Upload\DirectoryNamer\SubDirectoryNamer;
use PHPUnit\Framework\TestCase;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
class SubDirectoryNamerTest extends TestCase
{
    public function fileDataProvider()
    {
        return [
            ['0123456789.jpg', '01', 2, 1],
            ['0123456789.jpg', '01/23', 2, 2],
            ['0123456789.jpg', '012', 3, 1],
            ['0123456789.jpg', '0', 1, 1],
            ['0123456789.jpg', '0/1/2/3', 1, 4],
        ];
    }

    /**
     * @dataProvider fileDataProvider
     */
    public function testNameReturnsTheRightName($fileName, $expectedFileName, $charsPerDir, $dirs): void
    {
        $namer = new SubDirectoryNamer($charsPerDir, $dirs);
        $this->assertSame($expectedFileName, $namer->getDirectoryName($fileName));
    }
}
