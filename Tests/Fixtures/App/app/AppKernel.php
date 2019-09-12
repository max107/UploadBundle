<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Max107\Bundle\UploadBundle\UploadBundle(),
            new Max107\TestBundle\VichTestBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/VichUploaderBundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/VichUploaderBundle/logs';
    }
}
