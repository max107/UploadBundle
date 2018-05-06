<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\DirectoryNamer;

class SubDirectoryNamer implements DirectoryNamerInterface
{
    /**
     * @var int
     */
    protected $charsPerDir = 2;
    /**
     * @var int
     */
    protected $dirs = 1;

    /**
     * @param int $charsPerDir how many chars use for each dir
     * @param int $dirs        how many dirs create
     */
    public function __construct(int $charsPerDir = 2, int $dirs = 1)
    {
        $this->charsPerDir = $charsPerDir;
        $this->dirs = $dirs;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectoryName(string $name): string
    {
        $parts = [];
        for ($i = 0, $start = 0; $i < $this->dirs; $i++, $start += $this->charsPerDir) {
            $parts[] = substr($name, $start, $this->charsPerDir);
        }

        return implode('/', $parts);
    }
}
