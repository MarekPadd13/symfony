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

/**
 * Class UserController.
 *
 * @Route("/api", name="user_api")
 */
class UserController extends AbstractController
{
    private $repository;
    private $handlerUserRegistration;
    private $handlerUserConfirmation;

    public function __construct(UserRepository $repository, RegistrationHandlerInterface $handlerUserRegistration, ConfirmationHandlerInterface $handlerUserConfirmation)
    {
        $this->repository = $repository;
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
            if (!$request) {
                throw new \Exception('Error request', 500);
            }
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            if (!$email || !$password) {
                throw new \Exception('Data no valid', 422);
            }

            if ($this->repository->findByUserEmail($email)) {
                throw new \Exception('Email yes in data base', 422);
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);

            $this->handlerUserRegistration->handle($user);

            $data = [
                'status' => 200,
                'success' => 'User added successfully',
            ];

            return $this->response($data);
        } catch (\Exception $e) {
            $data = [
                'status' => $e->getCode(),
                'errors' => $e->getMessage(),
            ];
            return $this->response($data, $e->getCode());
        }
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     * @Route("/user/{id}", name="user_get", methods={"GET"})
     */
    public function getShow($id)
    {
        $user = $this->repository->findOneBySomeFieldEmailOrId($id);
        if (!$user) {
            return $this->response($this->getDataNotFound(), 404);
        }
        return $this->response($user);
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     * @Route("/confirm/{id}", name="user_confirm", methods={"PUT"})
     */
    public function confirm($id)
    {
        try {
            $user = $this->repository->find($id);
            if (!$user) {
                return $this->response($this->getDataNotFound(), 404);
            }
            $this->handlerUserConfirmation->handle($user);
            $data = [
                'status' => 200,
                'errors' => 'User updated successfully',
            ];
            return $this->response($data);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => $e->getMessage(),
            ];

            return $this->response($data, 422);
        }
    }

    /**
     * @param $id
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
            return $this->response($this->getDataNotFound(), 404);
        }
        $this->repository->remove($user);
        $data = [
            'status' => 200,
            'errors' => 'User deleted successfully',
        ];

        return $this->response($data);
    }

    /**
     * Returns a JSON response.
     *
     * @param array $data
     * @param $status
     * @param array $headers
     *
     * @return JsonResponse
     */
    private function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @return Request
     */
    private function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @return array
     */
    private function getDataNotFound()
    {
        $data = [
            'status' => 404,
            'errors' => 'User not found',
        ];

        return $data;
    }
}
