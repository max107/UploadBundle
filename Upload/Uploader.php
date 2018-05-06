<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload;

use League\Flysystem\FilesystemInterface;
use Max107\Bundle\UploadBundle\Upload\DirectoryNamer\DirectoryNamerInterface;
use Max107\Bundle\UploadBundle\Upload\FileNamer\FileNamerInterface;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    /**
     * @var FileNamerInterface
     */
    protected $fileNamer;
    /**
     * @var DirectoryNamerInterface
     */
    protected $directoryNamer;

    /**
     * Uploader constructor.
     *
     * @param FileNamerInterface      $fileNamer
     * @param DirectoryNamerInterface $directoryNamer
     */
    public function __construct(
        FileNamerInterface $fileNamer,
        DirectoryNamerInterface $directoryNamer
    ) {
        $this->fileNamer = $fileNamer;
        $this->directoryNamer = $directoryNamer;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function getFileExtension(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    /**
     * @param FilesystemInterface $filesystem
     * @param string              $uploadTo
     * @param string              $fileName
     *
     * @return string
     */
    protected function findAvailablePath(FilesystemInterface $filesystem, string $uploadTo, string $fileName): string
    {
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = $this->getFileExtension($fileName);

        $i = 0;
        $resolvedName = sprintf('%s.%s', $name, $ext);
        while ($filesystem->has(sprintf('%s/%s', $uploadTo, $resolvedName))) {
            ++$i;
            $resolvedName = sprintf('%s_%d.%s', $name, $i, $ext);
        }

        return sprintf('%s/%s', $uploadTo, $resolvedName);
    }

    /**
     * @param FilesystemInterface $filesystem
     * @param SplFileInfo         $uploadedFile
     *
     * @throws \League\Flysystem\FileExistsException
     *
     * @return string
     */
    public function upload(FilesystemInterface $filesystem, SplFileInfo $uploadedFile): string
    {
        $fileName = $uploadedFile instanceof UploadedFile ?
            $uploadedFile->getClientOriginalName() :
            $uploadedFile->getFilename();

        $uploadTo = $this->directoryNamer->getDirectoryName($fileName);
        $targetName = $this->fileNamer->getFileName($fileName);
        $destination = $this->findAvailablePath(
            $filesystem,
            $uploadTo,
            $targetName
        );

        $filesystem->write(
            $destination,
            file_get_contents($uploadedFile->getRealPath())
        );

        return $destination;
    }
}
