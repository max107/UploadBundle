<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\DirectoryNamer;

interface DirectoryNamerInterface
{
    /**
     * @param string $name
     * @return string
     */
    public function getDirectoryName(string $name): string;
}
