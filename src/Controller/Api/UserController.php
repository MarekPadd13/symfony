<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Handler\User\Confirmation\ConfirmationHandlerInterface;
use App\Handler\User\Registration\RegistrationHandlerInterface;
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
    const CODE_NOT_VALID = 422;
    const CODE_SUCCESS = 200;
    const CODE_NOT_FOUND = 404;
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var RegistrationHandlerInterface
     */
    private $handlerUserRegistration;

    /**
     * @var ConfirmationHandlerInterface
     */
    private $handlerUserConfirmation;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(UserRepository $repository,
                                TranslatorInterface $translator,
                                RegistrationHandlerInterface $handlerUserRegistration,
                                ConfirmationHandlerInterface $handlerUserConfirmation)
    {
        $this->repository = $repository;
        $this->translator = $translator;
        $this->handlerUserRegistration = $handlerUserRegistration;
        $this->handlerUserConfirmation = $handlerUserConfirmation;
    }

    /**
     * @return JsonResponse
     * @Route("/users", name="users", methods={"GET"})
     */
    public function getUsers()
    {
        $data = $this->repository->findBySelectEmailAndIsEnabled();

        return $this->response($data);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception
     * @Route("/add", name="user_add", methods={"POST"})
     */
    public function addUser(Request $request)
    {
        try {
            $request = $this->transformJsonBody($request);
            $email = $request->request->get('email');
            $password = $request->request->get('password');
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

            $this->handlerUserRegistration->handle($user);
            $message = $this->getUserAddedMessage();
            $data = $this->getDataSuccessMessage($message);

            return $this->response($data);
        } catch (\Exception $e) {
            $data = $this->getDataErrorMessage($e->getCode(), $e->getMessage());

            return $this->response($data, $e->getCode());
        }
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @Route("/user/{id}", name="user_get", methods={"GET"})
     */
    public function show($id)
    {
        $user = $this->repository->findOneBySomeFieldId($id);
        if (!$user) {
            return $this->responseNotFound();
        }

        return $this->response($user);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @Route("/confirm/{id}", name="user_confirm", methods={"PUT"})
     */
    public function confirm($id)
    {
        try {
            $user = $this->repository->find($id);
            if (!$user) {
                return $this->responseNotFound();
            }
            $this->handlerUserConfirmation->handle($user);
            $message = $this->getUserUpdatedMessage();
            $data = $this->getDataSuccessMessage($message);

            return $this->response($data);
        } catch (\Exception $e) {
            $data = $this->getDataErrorMessage($e->getCode(), $e->getMessage());

            return $this->response($data, $e->getCode());
        }
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete($id)
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

    /**
     * Returns a JSON response.
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    private function response($data, $status = self::CODE_SUCCESS, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Returns a JSON response.
     */
    private function responseNotFound(): JsonResponse
    {
        $data = $this->getDataNotFoundMessage();

        return new JsonResponse($data, self::CODE_NOT_FOUND);
    }

    /**
     * @param Request $request
     * @return Request
     */
    private function transformJsonBody(Request $request): Request
    {
        $data = $this->getDataJsonDecode($request);
        if (null === $data) {
            return $request;
        }
        $request->request->replace($data);

        return $request;
    }

    /**
     * @return mixed|null
     */
    private function getDataJsonDecode(Request $request)
    {
        $json = $request->getContent();
        $data = null;
        if (is_string($json)) {
            $data = json_decode($json, true);
        }

        return $data;
    }

    /**
     * @param string $message
     * @return array
     */
    private function getDataSuccessMessage(string $message): array
    {
        $data = [
            'status' => self::CODE_SUCCESS,
            'success' => $message,
        ];

        return $data;
    }

    /**
     * @return array
     */
    private function getDataNotFoundMessage(): array
    {
        $data = [
            'status' => self::CODE_NOT_FOUND,
            'errors' => $this->getNotFoundMessage(),
        ];

        return $data;
    }

    /**
     * @return array
     */
    private function getDataErrorMessage(int $code, string $message)
    {
        $data = [
            'status' => $code,
            'errors' => $message,
        ];

        return $data;
    }

    /**
     * @param string $id
     * @return string
     */
    private function getTranslationTrans($id): string
    {
        return $this->translator->trans($id);
    }

    /**
     * @return string
     */
    public function getNotValidMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.Data no valid');
    }

    /**
     * @return string
     */
    public function getEmailExistsMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.Such e-mail address already exists in the system');
    }

    /**
     * @return string
     */
    public function getNotFoundMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User not found');
    }

    /**
     * @return string
     */
    public function getUserAddedMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User added successfully');
    }

    /**
     * @return string
     */
    public function getUserUpdatedMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User updated successfully');
    }

    /**
     * @return string
     */
    public function getUserDeletedMessage(): string
    {
        return $this->getTranslationTrans('User.Api.messages.User deleted successfully');
    }
}
