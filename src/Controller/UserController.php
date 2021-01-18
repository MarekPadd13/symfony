<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailUserType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Handler\User\Confirmation\ConfirmationHandlerInterface;
use App\Handler\User\NewPassword\NewPasswordHandlerInterface;
use App\Handler\User\Registration\RegistrationHandlerInterface;
use App\Handler\User\Reset\ResetHandlerInterface;
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
     * @var ResetHandlerInterface
     */
    private $handlerUserReset;

    /**
     * @var NewPasswordHandlerInterface
     */
    private $handlerUserNewPassword;

    /**
     * UserController constructor.
     * @param ConfirmationHandlerInterface $handlerUserConfirmation
     * @param RegistrationHandlerInterface $handlerUserRegistration
     * @param ResetHandlerInterface $handlerUserReset
     * @param NewPasswordHandlerInterface $handlerUserNewPassword
     */
    public function __construct(ConfirmationHandlerInterface $handlerUserConfirmation,
                                RegistrationHandlerInterface $handlerUserRegistration,
                                ResetHandlerInterface $handlerUserReset, NewPasswordHandlerInterface $handlerUserNewPassword

    )
    {
        $this->handlerUserRegistration = $handlerUserRegistration;
        $this->handlerUserConfirmation = $handlerUserConfirmation;
        $this->handlerUserReset = $handlerUserReset;
        $this->handlerUserNewPassword = $handlerUserNewPassword;
    }

    /**
     * @Route("/registration", name="registration", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function registration(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerUserRegistration->handle($user);
            $this->addFlash('notice', $this->handlerUserRegistration->getSuccessMessage());

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
            $this->addFlash('success', $this->handlerUserConfirmation->getSuccessMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/reset", name="reset", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reset(Request $request): Response
    {
        $form = $this->createForm(EmailUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $request->request->get('email_user');
            $user = (new User())->setEmail($result['email']);
            try {
                $this->handlerUserReset->handle($user);
                $this->addFlash('success', $this->handlerUserReset->getSuccessMessage());
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('user/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add-new-password/{confirmToken}", name="add_new_password", methods={"GET","POST"})
     * @param User $user
     * @return Response
     */
    public function addNewPassword(Request $request, User $user): Response
    {
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerUserNewPassword->handle($user);
            $this->addFlash('success', $this->handlerUserNewPassword->getSuccessMessage());
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/new_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
