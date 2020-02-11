<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\FileNamer;

interface FileNamerInterface
{
    public function getFileName(string $name): string;
}
