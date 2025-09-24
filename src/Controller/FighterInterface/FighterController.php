<?php

namespace App\Controller\FighterInterface;

use App\Entity\Fighter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FighterService;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Psr\Log\LoggerInterface;

final class FighterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Lazy] private FighterService $fighterService,
        private LoggerInterface $logger,
    ) {}

    #[Route('/fighters', name: 'app_fighters', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try{
            return $this->fighterService->index($request);
        }catch(\Exception $e){
            $this->logger->error('Error fetching fighters: ' . $e->getMessage());
            throw new \Exception('Error fetching fighters: ' . $e->getMessage());
        }
    }

    #[Route('/fighter/{id}', name: 'app_fighter_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try{
            return $this->fighterService->show($id);
        }catch(\Exception $e){
            $this->logger->error('Error fetching fighter: ' . $e->getMessage());
            throw new \Exception('Error fetching fighter: ' . $e->getMessage());
        }
    }

    #[Route('/register-fighter', name: 'app_fighter_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try{
            return $this->fighterService->register($request);
        }catch(\Exception $e){
            $this->logger->error('Error registering fighter: ' . $e->getMessage());
            throw new \Exception('Error registering fighter: ' . $e->getMessage());
        }
    }

    #[Route('/fighter/{id}', name: 'app_fighter_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try{
            return $this->fighterService->update($id, $request);
        }catch(\Exception $e){
            $this->logger->error('Error updating fighter: ' . $e->getMessage());
            throw new \Exception('Error updating fighter: ' . $e->getMessage());
        }
    }
}
