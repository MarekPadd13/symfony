<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Factory\User\Confirmation;
use App\Factory\User\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationController extends AbstractController
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * ConfirmationController constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @Route("/confirm/{hash}", name="user_confirm", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function confirm(User $user): Response
    {
        try {
            $this->factory->confirmed($user);
            $this->addFlash('success', "Ok");
        }catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
         return $this->redirectToRoute('app_login');
    }
}
