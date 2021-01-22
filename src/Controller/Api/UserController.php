<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Handler\User\ConfirmationHandler;
use App\Handler\User\RegistrationHandler;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 *
 * @Route("/api", name="user_api")
 */
class UserController extends AbstractController
{
    private const CODE_NOT_VALID = 422;
    private const CODE_SUCCESS = 200;
    private const CODE_NOT_FOUND = 404;

    private UserRepository $repository;
    private RegistrationHandler $registrationHandler;
    private ConfirmationHandler $confirmationHandler;
    private TranslatorInterface $translator;

    public function __construct(
        UserRepository $repository,
        RegistrationHandler $registrationHandler,
        ConfirmationHandler $confirmationHandler,
        TranslatorInterface $translator
    ) {
        $this->repository = $repository;
        $this->registrationHandler = $registrationHandler;
        $this->confirmationHandler = $confirmationHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function getUsers(): JsonResponse
    {
        $data = $this->repository->findBySelectEmailAndIsEnabled();

        return $this->response($data);
    }

    /**
     * @Route("/add", name="user_add", methods={"POST"})
     *
     * @throws \Exception
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function addUser(Request $request): JsonResponse
    {
        try {
            $data= $this->getDataJsonDecode($request);
            if (!$data) {
                throw new \Exception('Error', 500);
            }
            $email = $data['email'];
            $password = $data['password'];
            if (!$email || !$password) {
                $message = $this->getNotValidMessage();
                throw new \Exception($message, self::CODE_NOT_VALID);
            }

            if ($this->repository->findByUserEmail($email)) {
                $message = $this->getEmailExistsMessage();
                throw new \Exception($message, self::CODE_NOT_VALID);
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);

            $this->registrationHandler->handle($user);
            $message = $this->getUserAddedMessage();
            $data = $this->getDataSuccessMessage($message);

            return $this->response($data);
        } catch (\Exception $e) {
            $data = $this->getDataErrorMessage($e->getCode(), $e->getMessage());

            return $this->response($data, $e->getCode());
        }
    }

    /**
     * @Route("/user/{id}", name="user_get", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->repository->findOneById($id);
        if (!$user) {
            return $this->responseNotFound();
        }

        return $this->response($user);
    }

    /**
     * @Route("/confirm/{id}", name="user_confirm", methods={"PUT"})
     */
    public function confirm(int $id): JsonResponse
    {
        try {
            $user = $this->repository->find($id);
            if (!$user) {
                return $this->responseNotFound();
            }
            $this->confirmationHandler->handle($user);
            $message = $this->getUserUpdatedMessage();
            $data = $this->getDataSuccessMessage($message);

            return $this->response($data);
        } catch (\Exception $e) {
            $data = $this->getDataErrorMessage($e->getCode(), $e->getMessage());

            return $this->response($data, $e->getCode());
        }
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(int $id): JsonResponse
    {
        $user = $this->repository->find($id);
        if (!$user) {
            return $this->responseNotFound();
        }
        $this->repository->remove($user);

        $message = $this->getUserDeletedMessage();
        $data = $this->getDataSuccessMessage($message);

        return $this->response($data);
    }

    private function response(array $data, int $status = self::CODE_SUCCESS, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    private function responseNotFound(): JsonResponse
    {
        $data = $this->getDataNotFoundMessage();

        return new JsonResponse($data, self::CODE_NOT_FOUND);
    }

    private function getDataJsonDecode(Request $request): ?array
    {
        $body = $request->getContent();
        $data = null;
        if (is_string($body)) {
            $data = json_decode($body, true);
        }

        return $data;
    }

    private function getDataSuccessMessage(string $message): array
    {
        $data = [
            'status' => self::CODE_SUCCESS,
            'success' => $message,
        ];

        return $data;
    }

    private function getDataNotFoundMessage(): array
    {
        $data = [
            'status' => self::CODE_NOT_FOUND,
            'errors' => $this->getNotFoundMessage(),
        ];

        return $data;
    }

    private function getDataErrorMessage(int $code, string $message): array
    {
        $data = [
            'status' => $code,
            'errors' => $message,
        ];

        return $data;
    }

    private function getTranslationTrans(string $id): string
    {
        return $this->translator->trans($id);
    }

    public function getNotValidMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.Data no valid');
    }

    public function getEmailExistsMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.Such e-mail address already exists in the system');
    }

    public function getNotFoundMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User not found');
    }

    public function getUserAddedMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User added successfully');
    }

    public function getUserUpdatedMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User updated successfully');
    }

    public function getUserDeletedMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User deleted successfully');
    }
}
