<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Max107\Bundle\UploadBundle\DependencyInjection\UploadExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;

class UploadExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new UploadExtension(),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.debug', false);
        $this->container->setParameter('kernel.bundles', []);
        $this->container->setParameter('kernel.bundles_metadata', []);
        $this->container->setParameter('kernel.project_dir', __DIR__ . '/../Fixtures/App');
        $this->container->setParameter('kernel.cache_dir', sys_get_temp_dir());
    }

    public function testFormThemeCorrectlyOverridden(): void
    {
        $vichUploaderExtension = new UploadExtension();
        $this->container->registerExtension($vichUploaderExtension);

        $twigExtension = new TwigExtension();
        $this->container->registerExtension($twigExtension);

        $twigExtension->load([['form_themes' => ['@Ololo/trololo.html.twig']]], $this->container);
        $vichUploaderExtension->load([], $this->container);

        $this->assertContainerBuilderHasParameter(
            'twig.form.resources',
            ['@Upload/form/flexy_fields.html.twig', 'form_div_layout.html.twig', '@Ololo/trololo.html.twig']
        );
    }
}
