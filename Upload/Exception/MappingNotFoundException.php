<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\Exception;

class MappingNotFoundException extends \RuntimeException
{
    public function __construct(string $mapping, string $class)
    {
        parent::__construct(sprintf(
            'Mapping "%s" does not exist. The configuration for the class "%s" is probably incorrect.',
            $mapping,
            $class
        ));
    }
}
