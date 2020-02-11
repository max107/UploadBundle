<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType as BaseFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractFileType extends AbstractType
{
    /**
     * @var FileEvent
     */
    protected $event;
    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param FileEvent $event
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(FileEvent $event, PropertyAccessor $propertyAccessor)
    {
        $this->event = $event;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->event->setFormBuilder($builder, $options['mapping']);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            $this->event
        );
    }

    /**
     * @param array|object $data
     *
     * @param string $name
     * @return string|null
     */
    public function resolveFileUrl($data, string $name): ?string
    {
        $value = '';
        if (is_array($data) && isset($data[$name]) || is_object($data)) {
            $value = $this->propertyAccessor->getValue($data, $name);
        }

        return is_string($value) ? $value : '';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($parent = $form->getParent()) {
            $view->vars['file_url'] = $this->resolveFileUrl($parent->getData(), $options['mapping']);
        } else {
            $view->vars['file_url'] = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return BaseFileType::class;
    }
}
