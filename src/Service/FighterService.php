<?php

namespace App\Service;

use App\Entity\Fighter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FighterService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function index(Request $request)
    {
        $params = $request->query->all('params');
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;
        $totalFighters = $this->entityManager->getRepository(Fighter::class)->count([]);
        $fighters = $this->entityManager->getRepository(Fighter::class)->findBy([], ['id' => 'DESC'], $perPage, $offset);
        $fightersArray = array_map(fn(Fighter $fighter) => $fighter->toArray(), $fighters);
        return new JsonResponse(['fighters' => $fightersArray, 'totalFighters' => $totalFighters, 'page' => $page, 'perPage' => $perPage]);
    }

    public function show(int $id): JsonResponse
    {
        $fighter = $this->entityManager->getRepository(Fighter::class)->find($id);
        return new JsonResponse(['fighter' => $fighter->toArray()]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $fighter = new Fighter();
        $fighter->setName($data['name']);
        $fighter->setNickname($data['nickname']);
        $fighter->setDescription($data['description']);
        $fighter->setWeightClass($data['weightClass']);
        $fighter->setWins($data['wins']);
        $fighter->setLosses($data['losses']);
        $fighter->setDraws($data['draws']);
        $fighter->setCountry($data['country']);
        $birthday = \DateTime::createFromFormat('d/m/Y', $data['birthday']);
        if ($birthday !== false) {
            $fighter->setBirthday($birthday);
        } else {
            throw new \InvalidArgumentException("Format de date invalide pour 'birthday'. Format attendu : jj/mm/aaaa");
        }
        $fighter->setIsActive($data['isActive']);
        !empty($data['height']) && $fighter->setHeight($data['height']);
        !empty($data['weight']) && $fighter->setWeight($data['weight']);
        !empty($data['reach']) && $fighter->setReach($data['reach']);
        !empty($data['stance']) && $fighter->setStance($data['stance']);
        !empty($data['style']) && $fighter->setStyle($data['style']);

        $this->entityManager->persist($fighter);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Fighter registered successfully']);
    }
}
