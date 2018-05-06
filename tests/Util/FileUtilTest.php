<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Util;

use Max107\Bundle\UploadBundle\Util\FileUtil;
use PHPUnit\Framework\TestCase;

class FileUtilTest extends TestCase
{
    /**
     * @dataProvider spitNameByExtensionProvider
     *
     * @param string $filename
     * @param string $basename
     * @param string $extension
     */
    public function testSpitNameByExtension(string $filename, string $basename, string $extension): void
    {
        $this->assertSame(
            [$basename, $extension],
            FileUtil::spitNameByExtension($filename)
        );
    }

    public function spitNameByExtensionProvider()
    {
        return [
            'simple filename with extension' => ['filename.extension', 'filename', 'extension'],
            'cyrillic filename with extension  ' => ['Текстовый файл.txt', 'Текстовый файл', 'txt'],
            'cyrillic filename with dot and extension' => ['Текстовый .файл.txt', 'Текстовый .файл', 'txt'],
            'cyrillic filename without extension ends with dot' => ['Текстовый файл.', 'Текстовый файл', ''],
            'cyrillic filename without extension' => ['Текстовый файл', 'Текстовый файл', ''],
        ];
    }
}
