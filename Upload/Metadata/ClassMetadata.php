<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\Metadata;

use Metadata\ClassMetadata as BaseClassMetadata;

class ClassMetadata extends BaseClassMetadata
{
    /**
     * @var array<mixed>
     */
    public $fields = [];

    public function serialize(): string
    {
        return serialize([
            $this->fields,
            parent::serialize(),
        ]);
    }

    public function unserialize($str): void
    {
        [$this->fields, $parentStr] = unserialize($str);

        parent::unserialize($parentStr);
    }
}
