<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Metadata;

use Max107\Bundle\UploadBundle\Upload\Metadata\ClassMetadata;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    public function testFieldsAreSerialized(): void
    {
        $fields = ['foo', 'bar', 'baz'];
        $metadata = new ClassMetadata('DateTime');
        $metadata->fields = $fields;

        $deserializedMetadata = unserialize(serialize($metadata));

        $this->assertSame($fields, $deserializedMetadata->fields);
    }
}
