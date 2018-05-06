<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload\Annotation;

/**
 * UploadableField.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author Dustin Dobervich <ddobervich@gmail.com>
 */
class UploadableField
{
    /**
     * @var string
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $originalName;

    /**
     * @var array
     */
    protected $dimensions;

    /**
     * Constructs a new instance of UploadableField.
     *
     * @param array $options The options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (empty($options['filesystem'])) {
            throw new \InvalidArgumentException('The "filesystem" attribute of UploadableField is required.');
        }

        if (empty($options['path'])) {
            throw new \InvalidArgumentException('The "path" attribute of UploadableField is required.');
        }

        foreach ($options as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new \RuntimeException(sprintf('Unknown key "%s" for annotation "@%s".', $property, get_class($this)));
            }

            $this->$property = $value;
        }
    }

    /**
     * Gets the mapping name.
     *
     * @return string The mapping name
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Gets the file name property.
     *
     * @return string The file name property
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @return array|null
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }
}
