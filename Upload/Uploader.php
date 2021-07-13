<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Upload;

use League\Flysystem\MountManager;
use Max107\Bundle\UploadBundle\Upload\DirectoryNamer\DirectoryNamerInterface;
use Max107\Bundle\UploadBundle\Upload\FileNamer\FileNamerInterface;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    protected FileNamerInterface $fileNamer;
    protected DirectoryNamerInterface $directoryNamer;

    public function __construct(
        FileNamerInterface $fileNamer,
        DirectoryNamerInterface $directoryNamer
    ) {
        $this->fileNamer = $fileNamer;
        $this->directoryNamer = $directoryNamer;
    }

    protected function getFileExtension(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    protected function findAvailablePath(
        MountManager $filesystem,
        string $uploadTo,
        string $fileName,
        string $filesystemName
    ): string {
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = $this->getFileExtension($fileName);

        $i = 0;
        $resolvedName = sprintf('%s.%s', $name, $ext);
        while ($filesystem->fileExists(sprintf('%s://%s/%s', $filesystemName, $uploadTo, $resolvedName))) {
            ++$i;
            $resolvedName = sprintf('%s_%d.%s', $name, $i, $ext);
        }

        return sprintf('%s/%s', $uploadTo, $resolvedName);
    }

    public function upload(
        MountManager $filesystem,
        SplFileInfo $uploadedFile,
        string $filesystemName
    ): string {
        $fileName = $uploadedFile instanceof UploadedFile ?
            $uploadedFile->getClientOriginalName() :
            $uploadedFile->getFilename();

        $uploadTo = $this->directoryNamer->getDirectoryName($fileName);
        $targetName = $this->fileNamer->getFileName($fileName);
        $destination = $this->findAvailablePath(
            $filesystem,
            $uploadTo,
            $targetName,
            $filesystemName
        );

        $filesystem->write(
            sprintf('%s://%s', $filesystemName, $destination),
            file_get_contents($uploadedFile->getRealPath())
        );

        return $destination;
    }
}
