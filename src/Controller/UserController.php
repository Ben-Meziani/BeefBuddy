<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\UserService;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function __construct(
        #[Lazy] private UserService $userService,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/user/{id}', name: 'app_user', methods: ['GET'])]
    public function index($id)
    {
        try{
            return $this->userService->index($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete($id)
    {
        try{
            return $this->userService->delete($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/user/{id}', name: 'app_user_update', methods: ['PUT'])]
    public function update($id, Request $request)
    {
        try{
            return $this->userService->update($id, $request, $this->getParameter('mail_from'), $this->getParameter('mail_from_name'));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
