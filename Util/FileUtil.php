<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Util;

final class FileUtil
{
    /**
     * Splits filename for array of basename and extension.
     *
     * @param string $filename
     *
     * @return array An array of basename and extension
     */
    public static function spitNameByExtension(string $filename): array
    {
        if (false === $pos = strrpos($filename, '.')) {
            return [$filename, ''];
        }

        return [substr($filename, 0, $pos), substr($filename, $pos + 1)];
    }
}
