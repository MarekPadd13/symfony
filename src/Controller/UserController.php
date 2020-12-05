<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\User\ConfirmationMailMailer;
use App\Factory\User\Factory;
use App\Factory\User\Password;
use App\Factory\User\Registration;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * UserController constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @Route("/registration", name="registration", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function registration(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factory->createTo($user);
            $this->addFlash('notice', "Confirm your email");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/registration.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
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
