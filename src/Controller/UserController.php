<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Handler\User\Confirmation\ConfirmationHandlerInterface;
use App\Handler\User\Registration\RegistrationHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var RegistrationHandlerInterface
     */
    private $handlerUserRegistration;

    /**
     * @var ConfirmationHandlerInterface
     */
    private $handlerUserConfirmation;

    /**
     * UserController constructor.
     * @param ConfirmationHandlerInterface $handlerUserConfirmation
     * @param RegistrationHandlerInterface $handlerUserRegistration
     */
    public function __construct(ConfirmationHandlerInterface $handlerUserConfirmation, RegistrationHandlerInterface $handlerUserRegistration)
    {
        $this->handlerUserRegistration = $handlerUserRegistration;
        $this->handlerUserConfirmation = $handlerUserConfirmation;
    }

    /**
     * @Route("/registration", name="registration", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function registration(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerUserRegistration->handle($user);
            $this->addFlash('notice', $this->handlerUserRegistration->successMessage());
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/registration.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{confirmToken}", name="user_confirm", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function confirm(User $user): Response
    {
        try {
            $this->handlerUserConfirmation->handle($user);
            $this->addFlash('success', "Ok");
        }catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('app_login');
    }
}