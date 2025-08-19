<?php

namespace App\Service;

use App\Entity\Fighter;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\EmailService;
class ReservationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private EmailService $emailService,
    ) {}

    public function createReservation(Request $request){
        $data = json_decode($request->getContent(), true);
        $reservation = new Reservation();
        $fighter = $this->entityManager->getRepository(Fighter::class)->find($data['fighterId']);

        $user = $this->entityManager->getRepository(User::class)->find($data['userId']);
        $reservation->setFighter($fighter);
        $reservation->setUser($user);
        $reservation->setTotalPrice($data['totalPrice']);
        $reservation->setStartAt(new \DateTimeImmutable($data['dates'][0]));
        $reservation->setEndAt(new \DateTimeImmutable($data['dates'][0] ?? $data['dates'][1]));

        // Valider l'entité avant de la persister
        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
        $this->emailService->sendEmail($user, 'Reservation created', 'reservation/reservation.html.twig', [
            'reservation' => $reservation,
            'user' => $user,
            'fighter' => $fighter,
        ]);
        return new JsonResponse(['message' => 'Reservation created']);
    }

    public function getUserReservation(int $id){
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        $reservations = $this->entityManager->getRepository(Reservation::class)->findBy(['user' => $user]);
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'fighter' => $reservation->getFighter()->getName(),
                'totalPrice' => $reservation->getTotalPrice(),
                'startAt' => $reservation->getStartAt(),
                'endAt' => $reservation->getEndAt(),
            ];
        }
        return new JsonResponse(['reservations' => $data]);
    }
}
