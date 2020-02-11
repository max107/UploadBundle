<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\TestBundle\Controller;

use Max107\Bundle\UploadBundle\Form\Type as VichType;
use Max107\TestBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function uploadAction($formType)
    {
        $form = $this->getForm($formType, $this->getImage());

        return $this->render('VichTestBundle:Default:upload.html.twig', [
            'formType' => $formType,
            'form'     => $form->createView(),
        ]);
    }

    public function editAction($formType, $imageId)
    {
        $form = $this->getForm($formType, $this->getImage($imageId));

        return $this->render('VichTestBundle:Default:edit.html.twig', [
            'imageId'  => $imageId,
            'formType' => $formType,
            'form'     => $form->createView(),
        ]);
    }

    public function removeAction($imageId)
    {
        $image = $this->getImage($imageId);
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return new Response('ok', 200);
    }

    public function submitAction(Request $request, $formType, $imageId = null)
    {
        $image = $this->getImage($imageId);
        $form = $this->getForm($formType, $image);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($image);
            $em->flush();

            return $this->redirect($this->generateUrl('view', [
                'formType' => $formType,
                'imageId'  => $image->getId(),
            ]));
        }

        return $this->render('VichTestBundle:Default:upload.html.twig', [
            'formType' => $formType,
            'form'     => $form->createView(),
        ]);
    }

    private function getForm($fileType, Image $image)
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
