<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractFileType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'file_preview';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['asset_name'] = $options['asset_name'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['mapping'])
            ->setDefaults([
                'asset_name' => null,
                'data_class' => null,
                'mapping'    => null,
            ]);
    }
}
