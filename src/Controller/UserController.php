<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\UserService;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
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
}
