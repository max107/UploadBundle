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

class ImageType extends AbstractFileType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'image_preview';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['asset_name'] = $options['asset_name'];
        $view->vars['imagine_filter'] = $options['imagine_filter'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['mapping', 'imagine_filter'])
            ->setDefaults([
                'asset_name' => null,
                'imagine_filter' => null,
                'data_class' => null,
                'mapping' => null,
            ]);
    }
}
