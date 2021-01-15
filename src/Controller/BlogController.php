<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog_list", methods={"GET","HEAD"})
     */
    public function list(): Response
    {
        return $this->render('lucky/number.html.twig', ['number' => 125]);
    }

    /**
     * @param $id
     * @Route("/blog/view/{id?1}",
     *      name="blog_view",
     *     methods={"GET","HEAD"},
     *     requirements={"id"="\d+"},
     *    condition="context.getMethod() in ['GET', 'HEAD'] and request.headers.get('User-Agent') matches '/firefox/i'"
     * )
     * @return Response
     */
    public function view(int $id): Response
    {
        return $this->render('lucky/number.html.twig', ['number' => $id]);
    }

    /**
     * @Route("/blog/index", name="blog_index", methods={"GET","HEAD"})
     */
    public function index(): RedirectResponse
    {
        // redirects to the "homepage" route
        //return $this->redirectToRoute('blog_view', ['id'=> 45]);

        // redirectToRoute is a shortcut for:
        // return new RedirectResponse($this->generateUrl('homepage'));

        // does a permanent - 301 redirect
//        return $this->redirectToRoute('homepage', [], 301);
//
//        // redirect to a route with parameters
//        return $this->redirectToRoute('app_lucky_number', ['max' => 10]);
//
//        // redirects to a route and maintains the original query string parameters
//        return $this->redirectToRoute('blog_show', $request->query->all());
//
//        // redirects externally
        return $this->redirect('http://symfony.com/doc');
    }
}
