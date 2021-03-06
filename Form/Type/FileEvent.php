<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FileEvent
{
    const CLEAR_VALUE = '__remove';

    protected FormBuilderInterface $builder;
    protected ?Request $request;
    protected PropertyAccessor $propertyAccessor;
    protected string $mapping;

    public function __construct(RequestStack $requestStack, PropertyAccessor $propertyAccessor)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->propertyAccessor = $propertyAccessor;
    }

    public function setFormBuilder(FormBuilderInterface $builder, string $mapping): void
    {
        $this->builder = $builder;
        $this->mapping = $mapping;
    }

    public function fetchParams(string $path)
    {
        return $this->propertyAccessor->getValue(
            $this->request->request->all(),
            $path
        );
    }

    public function resolveParentPath(FormInterface $form): string
    {
        $path = [
            $form->getName(),
        ];
        $parent = $form->getParent();
        while (null !== $parent) {
            $path[] = $parent->getName();
            $parent = $parent->getParent();
        }

        return implode('', array_map(function ($item) {
            return sprintf('[%s]', $item);
        }, array_reverse($path)));
    }

    public function __invoke(FormEvent $event): void
    {
        $form = $event->getForm();

        $value = $this->fetchParams($this->resolveParentPath($form));

        if (null === $value && null === $event->getData()) {
            $field = $form->getParent();
            if ($field) {
                $field->remove($this->builder->getName());
            }
        } elseif (self::CLEAR_VALUE === $value) {
            $field = $form->getParent();
            if ($field) {
                $entity = $field->getData();
                $this->propertyAccessor->setValue(
                    $entity,
                    $this->mapping,
                    null
                );
            }
        }
    }
}
