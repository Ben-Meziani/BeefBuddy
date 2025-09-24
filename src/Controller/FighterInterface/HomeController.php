<?php

namespace App\Controller\FighterInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FighterInterface\HomeService;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

class HomeController extends AbstractController
{
    public function __construct(
        #[Lazy] private HomeService $homeService
    ) {}

    #[Route('/home-fighter', name: 'app_home_fighter')]
    public function index(Request $request): JsonResponse
    {
        try {
            return $this->homeService->index($request);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}


