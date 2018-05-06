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

    /**
     * @var FormBuilderInterface
     */
    protected $builder;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var PropertyAccessor
     */
    protected $accessor;
    /**
     * @var string
     */
    protected $mapping;

    /**
     * FileEvent constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->accessor = new PropertyAccessor();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string               $mapping
     */
    public function setFormBuilder(FormBuilderInterface $builder, string $mapping)
    {
        $this->builder = $builder;
        $this->mapping = $mapping;
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function fetchParams(string $path)
    {
        $parameters = $this->request->request->all();

        return $this->accessor->getValue($parameters, $path);
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

    /**
     * @param FormEvent $event
     */
    public function __invoke(FormEvent $event)
    {
        $form = $event->getForm();

        $path = $this->resolveParentPath($form);
        $value = $this->fetchParams($path);

        if (null === $value && null === $event->getData()) {
            $form->getParent()->remove($this->builder->getName());
        } elseif (self::CLEAR_VALUE === $value) {
            $entity = $form->getParent()->getData();
            $this->accessor->setValue($entity, $this->mapping, null);
        }
    }
}
