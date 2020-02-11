<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Form\Type;

use Max107\Bundle\UploadBundle\Form\Type\FileEvent;
use Max107\Bundle\UploadBundle\Form\Type\FileType;
use Max107\Bundle\UploadBundle\Form\Type\ImageType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FileTypeTest extends TestCase
{
    public function testFileType()
    {
        $fileType = new FileType(
            $this->createMock(FileEvent::class),
            $this->createMock(PropertyAccessor::class)
        );
        $this->assertSame('file_preview', $fileType->getBlockPrefix());

        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setRequired')
            ->with(['mapping'])
            ->willReturn($resolver)
        ;
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with([
                'asset_name' => null,
                'data_class' => null,
                'mapping'    => null,
            ])
            ->willReturn($resolver)
        ;
        $fileType->configureOptions($resolver);

        $view = new FormView();
        $form = $this->createMock(FormInterface::class);
        $form->method('getParent')->willReturn($form);
        $form->method('getData')->willReturn('/');
        $fileType->buildView($view, $this->createMock(FormInterface::class), [
            'asset_name' => 'test',
        ]);
        $this->assertSame('test', $view->vars['asset_name']);
    }

    public function testImageType()
    {
        $fileType = new ImageType(
            $this->createMock(FileEvent::class),
            $this->createMock(PropertyAccessor::class)
        );
        $this->assertSame('image_preview', $fileType->getBlockPrefix());

        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setRequired')
            ->with(['mapping', 'imagine_filter'])
            ->willReturn($resolver)
        ;
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with([
                'asset_name'     => null,
                'data_class'     => null,
                'mapping'        => null,
                'imagine_filter' => null,
            ])
            ->willReturn($resolver)
        ;
        $fileType->configureOptions($resolver);

        $view = new FormView();
        $form = $this->createMock(FormInterface::class);
        $form->method('getParent')->willReturn($form);
        $form->method('getData')->willReturn('/');
        $fileType->buildView($view, $form, [
            'mapping'        => 'test',
            'asset_name'     => 'test',
            'imagine_filter' => 'test',
        ]);
        $this->assertSame('test', $view->vars['asset_name']);
        $this->assertSame('test', $view->vars['imagine_filter']);
    }
}
