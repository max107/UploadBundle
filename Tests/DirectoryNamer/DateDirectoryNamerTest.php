<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Naming;

use Max107\Bundle\UploadBundle\Upload\DirectoryNamer\DateDirectoryNamer;
use PHPUnit\Framework\TestCase;

class DateDirectoryNamerTest extends TestCase
{
    public function testNameReturnsTheRightName(): void
    {
        $namer = new DateDirectoryNamer();
        $this->assertSame(strtr('%Y-%m-%d', [
            '%Y' => date('Y'),
            '%m' => date('m'),
            '%d' => date('d'),
            '%H' => date('H'),
            '%i' => date('i'),
            '%s' => date('s'),
        ]), $namer->getDirectoryName('test'));
    }
}
