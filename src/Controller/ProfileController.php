<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Handler\Profile\ProfileHandlerInterface;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @var ProfileRepository
     */
    private $repository;

    /**
     * @var ProfileHandlerInterface
     */
    private $handler;

    /**
     * ProfileController constructor.
     * @param ProfileRepository $repository
     * @param ProfileHandlerInterface $handler
     */
    public function __construct(ProfileRepository $repository, ProfileHandlerInterface $handler)
    {
        $this->repository = $repository;
        $this->handler = $handler;
    }


    /**
     * @Route("/profile", name="profile", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function profile(Request $request): Response
    {
        if (!$this->getUser()) {
             return $this->redirectToRoute('app_login');
        }

        $profile = $this->repository->findOneByUser($this->getUser()) ?? new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handler->setUser($this->getUser());
            $this->handler->handle($profile);
            $this->addFlash('notice', "Success!");

            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
