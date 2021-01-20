<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Helper\ProfileHelper;
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
     * ProfileController constructor.
     */
    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/profile", name="profile", methods={"GET","POST"})
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function formView(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $profile = $this->getProfile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$profile->getPatronymic()) {
                $profile->setPatronymic(ProfileHelper::DEFAUlT_PATRONYMIC);
            }
            $this->repository->save($profile);
            $this->addFlash('notice', 'Success!');

            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Profile
     * @throws \Exception
     */
    private function getProfile(): Profile
    {
        if (!$this->getUser()) {
            throw new \Exception('syke');
        }

        return $this->repository->findOneByUser($this->getUser()) ?? new Profile($this->getUser());
    }
}
