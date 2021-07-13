<?php

declare(strict_types=1);

namespace Max107\Bundle\UploadBundle\Tests;

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Max107\Bundle\UploadBundle\Tests\Bundle\TestBundle\VichTestBundle;
use Max107\Bundle\UploadBundle\UploadBundle;
use Oneup\FlysystemBundle\OneupFlysystemBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new OneupFlysystemBundle(),
            new LiipImagineBundle(),
            new UploadBundle(),
            new VichTestBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function getCacheDir(): string
    {
        return __DIR__ . '/var/cache';
    }

    public function getLogDir(): string
    {
        return __DIR__ . '/var/logs';
    }
}
