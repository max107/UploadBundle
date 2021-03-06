<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Bundle\TestBundle\Controller;

use Max107\Bundle\UploadBundle\Form\Type as VichType;
use Max107\Bundle\UploadBundle\Tests\Bundle\TestBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            'container' => ContainerInterface::class,
        ]);
    }

    public function upload($formType)
    {
        $form = $this->getForm($this->getImage());

        return $this->render('upload.html.twig', [
            'formType' => $formType,
            'form'     => $form->createView(),
        ]);
    }

    public function edit($formType, $imageId)
    {
        $form = $this->getForm($this->getImage($imageId));

        return $this->render('edit.html.twig', [
            'imageId'  => $imageId,
            'formType' => $formType,
            'form'     => $form->createView(),
        ]);
    }

    public function remove($imageId)
    {
        $image = $this->getImage($imageId);
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return new Response('ok', 200);
    }

    public function submit(Request $request, $formType, $imageId = null)
    {
        $image = $this->getImage($imageId);
        $form = $this->getForm($image);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($image);
                $em->flush();

                return $this->redirect($this->generateUrl('view', [
                    'formType' => $formType,
                    'imageId'  => $image->getId(),
                ]));
            }
        }

        return $this->render('upload.html.twig', [
            'formType' => $formType,
            'form'     => $form->createView(),
        ]);
    }

    private function getForm(Image $image)
    {
        return $this->createFormBuilder($image)
            ->add('imageFile', VichType\ImageType::class, [
                'mapping'        => 'imageName',
                'imagine_filter' => 'preview',
            ])
            ->add('save', Type\SubmitType::class)
            ->getForm()
        ;
    }

    private function getImage($imageId = null)
    {
        if (null === $imageId) {
            return new Image();
        }
        $image = $this->getDoctrine()->getRepository('VichTestBundle:Image')->find($imageId);

        return $image;
    }
}
