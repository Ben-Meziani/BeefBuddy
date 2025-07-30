<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\HomeService;

final class HomeController extends AbstractController
{
    public function __construct(
        private HomeService $homeService
    ) {}

    #[Route('/home', name: 'app_home')]
    public function index(
        Request $request
    ): JsonResponse
    {
        try {
            return $this->homeService->index($request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
