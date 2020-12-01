<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/registration", name="registration", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function registration(Request $request): Response
    {
//         if ($this->getUser()) {
//             return $this->redirectToRoute('home');
//         }
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->service->registry($user);
            $this->addFlash('notice', "Confirm your email");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registration.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{hash}", name="user_confirm", methods={"GET"})
     * @param User $user
     * @return Response
     */

    public function show(User $user): Response
    {
        try {
            $this->service->confirm($user);
            $this->addFlash('success', "Ok");
        }catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
         return $this->redirectToRoute('app_login');
    }


}
