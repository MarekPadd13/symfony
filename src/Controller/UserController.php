<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailUserType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Handler\User\ConfirmationHandler;
use App\Handler\User\NewPasswordHandler;
use App\Handler\User\RegistrationHandler;
use App\Handler\User\ResetPasswordHandler;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private RegistrationHandler $registrationHandler;
    private ConfirmationHandler $confirmationHandler;
    private ResetPasswordHandler $resetPasswordHandler;
    private NewPasswordHandler $newPasswordHandler;

    public function __construct(
        RegistrationHandler $registrationHandler,
        ConfirmationHandler $confirmationHandler,
        ResetPasswordHandler $resetPasswordHandler,
        NewPasswordHandler $newPasswordHandler
    ) {
        $this->registrationHandler = $registrationHandler;
        $this->confirmationHandler = $confirmationHandler;
        $this->resetPasswordHandler= $resetPasswordHandler;
        $this->newPasswordHandler = $newPasswordHandler;
    }

    /**
     * @Route("/registration", name="registration", methods={"GET","POST"})
     *
     * @throws ORMException
     * @throws TransportExceptionInterface
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function registration(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $handler = $this->registrationHandler;
            $handler->handle($user);
            $this->addFlash('notice', $handler->getSuccessMessage());

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/registration.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{confirmToken}", name="user_confirm", methods={"GET"})
     */
    public function confirm(User $user): Response
    {
        try {
            $handler = $this->confirmationHandler;
            $handler->handle($user);
            $this->addFlash('success', $handler->getSuccessMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/reset", name="reset", methods={"GET","POST"})
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function reset(Request $request): Response
    {
        $form = $this->createForm(EmailUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $request->request->get('email_user');
            $user = new User();
            $user->setEmail($result['email']);
            try {
                $handler = $this->resetPasswordHandler;
                $handler->handle($user);
                $this->addFlash('success', $handler->getSuccessMessage());

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
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addNewPassword(Request $request, User $user): Response
    {
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $handler = $this->newPasswordHandler;
            $handler->handle($user);
            $this->addFlash('success', $handler->getSuccessMessage());

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
