<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MakeController extends AbstractController
{
    /**
     * @Route("/make", name="make")
     */
    public function index(): Response
    {
        return $this->render('make/index.html.twig', [
            'controller_name' => 'MakeController',
        ]);
    }

    /**
     * @Route("/make/view/{id}", name="make_view")
     *
     * @param int $id
     * @return Response
     */
    public function view($id = 1): Response
    {
        if (2 == $id) {
            //  throw  $this->createNotFoundException("Такой страницы не существует");
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('brand_new');
        }

        return $this->render('make/index.html.twig', [
            'controller_name' => 'MakeController',
        ]);
    }
}
