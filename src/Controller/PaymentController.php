<?php

namespace App\Controller;

use App\Entity\Fighter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ReservationService;
use App\Service\PaymentService;

final class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationService $reservationService,
        private PaymentService $paymentService,
    ) {}

    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/checkout', name: 'api_checkout', methods: ['POST'])]
    public function checkout(Request $request): JsonResponse
    {
        try{
            return $this->paymentService->checkout($request, $this->getParameter('host_front'));
        }catch(\Exception $e){
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
