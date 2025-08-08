<?php

namespace App\Controller;

use App\Entity\Fighter;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ReservationService;

final class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private ReservationService $reservationService,
    ) {}

    #[Route('/reservation', name: 'app_reservation', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        try {
            return $this->reservationService->createReservation($request);

        }catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
