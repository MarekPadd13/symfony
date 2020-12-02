<?php

namespace App\Controller\User;

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

class RegistrationController extends AbstractController
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * RegistrationController constructor.
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
     * @throws \Exception
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

        return $this->render('security/registration.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
