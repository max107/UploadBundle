<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Functional;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Max107\Bundle\UploadBundle\Tests\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass()
    {
        return AppKernel::class;
    }

    protected function getUploadedFile($client, $name, $mimeType = 'image/png')
    {
        return new UploadedFile(
            $this->getImagesDir($client) . DIRECTORY_SEPARATOR . $name,
            $name,
            $mimeType,
            123
        );
    }

    protected function getUploadsDir()
    {
        return self::getContainer()->getParameter('kernel.cache_dir') . '/../media';
    }

    protected function getImagesDir($client)
    {
        return $client->getKernel()->getProjectDir() . '/Tests/images';
    }

    protected function loadFixtures(): void
    {
        $container = $this->client->getContainer();
        $registry = $container->get('doctrine');
        if ($registry instanceof ManagerRegistry) {
            $om = $registry->getManager();
        } else {
            $om = $registry->getEntityManager();
        }

        $cacheDriver = $om->getMetadataFactory()->getCacheDriver();
        if ($cacheDriver) {
            $cacheDriver->deleteAll();
        }

        $connection = $om->getConnection();
        $params = $connection->getParams();
        $name = $params['path'] ?? ($params['dbname'] ?? false);

        if (!$name) {
            throw new \InvalidArgumentException("Connection does not contain a 'path' or 'dbname' parameter and cannot be dropped.");
        }

        $metadatas = $om->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($om);
        $schemaTool->dropDatabase();
        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }
    }
}
