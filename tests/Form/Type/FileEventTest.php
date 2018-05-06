<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Form\Type;

use Max107\Bundle\UploadBundle\Form\Type\FileEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FileEventTest extends TestCase
{
    public function testInvoke()
    {
        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->at(1))
            ->method('getParent')
            ->willReturn(null);
        $form
            ->expects($this->at(2))
            ->method('getParent')
            ->willReturn($form);
        $form
            ->method('getName')
            ->willReturn('test');
        $form
            ->expects($this->once())
            ->method('remove')
            ->with('test');

        $formEvent = $this->createMock(FormEvent::class);
        $formEvent
            ->method('getForm')
            ->willReturn($form);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn(new Request());

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->method('getName')
            ->willReturn('test');

        $fileEvent = new FileEvent($requestStack);
        $fileEvent->setFormBuilder($formBuilder, 'test');
        call_user_func_array($fileEvent, [$formEvent]);
    }
}
