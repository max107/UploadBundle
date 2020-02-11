<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\DirectoryNamer;

class DateDirectoryNamer implements DirectoryNamerInterface
{
    /**
     * Upload to template, you can use these variables:
     * %Y - Current year (4 digits)
     * %m - Current month
     * %d - Current day of month
     * %H - Current hour
     * %i - Current minutes
     * %s - Current seconds
     *
     * @var string
     */
    protected $format = '%Y-%m-%d';

    /**
     * @param string $format
     */
    public function __construct(string $format = '%Y-%m-%d')
    {
        $this->format = $format;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getDirectoryName(string $name): string
    {
        return strtr($this->format, [
            '%Y' => date('Y'),
            '%m' => date('m'),
            '%d' => date('d'),
            '%H' => date('H'),
            '%i' => date('i'),
            '%s' => date('s'),
        ]);
    }
}
