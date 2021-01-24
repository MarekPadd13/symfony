<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\RegisterType;
use App\Handler\User\ConfirmationHandler;
use App\Handler\User\RegistrationHandler;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 *
 * @Route("/api", name="user_api")
 */
class UserController extends AbstractController
{
    private const CODE_SUCCESS = 200;
    private const CODE_NOT_CONTENT = 204;
    private const CODE_NOT_VALID = 424;
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
     * @throws ExceptionInterface
     */
    public function getUsers(): JsonResponse
    {
        $query = $this->repository->findAll();

        $data = $this->getSerialisedNormaliseData($query, [AbstractNormalizer::ATTRIBUTES => ['id','email','isEnabled']]);
        return $this->response($data);
    }

    /**
     * @Route("/user/add", name="user_add", methods={"POST"})
     *
     * @throws \Exception
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function addUser(Request $request): JsonResponse
    {
        try {
            $data = $this->getJsonDecodeData($request);
            if (!$data) {
                throw new \Exception($this->translator->trans('Not data'), self::CODE_NOT_CONTENT);
            }
            $user = new User();
            $form = $this->createForm(RegisterType::class, $user, ['csrf_protection' => false]);
            $form->submit($data);
            if (!$form->isValid()) {
                return $this->response($this->getErrorsFromForm($form), self::CODE_NOT_VALID);
            }
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
     * @throws ExceptionInterface
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->repository->findById($id);
        if (!$user) {
            return $this->responseNotFound();
        }
        $options = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (User $user) {
                return $user->getEmail();
            },
            AbstractNormalizer::ATTRIBUTES=> ['id','email','isEnabled', 'profile']
        ];
        $data = $this->getSerialisedNormaliseData($user, $options);

        return $this->response($data);
    }

    /**
     * @Route("/user/confirm/{id}", name="user_confirm", methods={"PUT"})
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

    private function getJsonDecodeData(Request $request): array
    {
        $body = $request->getContent();
        if (is_string($body)) {
            $data = json_decode($body, true);
        }

        return $data ?? [];
    }

    private function getDataSuccessMessage(string $message): array
    {
        $data = [
            'code' => self::CODE_SUCCESS,
            'message' => $message,
        ];

        return $data;
    }

    private function getDataNotFoundMessage(): array
    {
        $data = $this->getDataErrorMessage(self::CODE_NOT_FOUND, $this->getNotFoundMessage());
        return $data;
    }

    private function getDataErrorMessage(int $code, string $message): array
    {
        $data = [
            'error' => [
            'code' => $code,
            'message' => $message,
            ]
        ];

        return $data;
    }

    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */

    private function getSerialisedNormaliseData(array $data, array $options = []): array
    {
        $normalizer = new ObjectNormalizer();

        $serializer = new Serializer([$normalizer]);
        $data = $serializer->normalize($data, 'null', $options);

        if (!is_array($data)) {
            throw new \Exception($this->translator->trans('Not data'), self::CODE_NOT_CONTENT);
        }

        return $data;
    }

    private function getNotFoundMessage(): string
    {
        return $this->translator->trans('User.Api.messages.User not found');
    }

    private function getUserAddedMessage(): string
    {
        return $this->translator->trans('User.Api.messages.User added successfully');
    }

    private function getUserUpdatedMessage(): string
    {
        return $this->translator->trans('User.Api.messages.User updated successfully');
    }

    private function getUserDeletedMessage(): string
    {
        return $this->translator->trans('User.Api.messages.User deleted successfully');
    }
}
