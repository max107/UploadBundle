<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\FileNamer;

use Max107\Bundle\UploadBundle\Util\FileUtil;

class MD5FileNamer implements FileNamerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFileName(string $name): string
    {
        list($name, $extension) = FileUtil::spitNameByExtension($name);

        return sprintf('%s.%s', md5($name), $extension);
    }
}
