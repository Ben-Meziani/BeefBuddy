<?php

namespace App\Tests;

use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private $databaseTool;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->databaseTool = static::getContainer()
            ->get(DatabaseToolCollection::class)
            ->get();
    }

    public function testCount(): void
    {
       $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $users = $entityManager->getRepository(\App\Entity\User::class)->findAll();
        $this->assertCount(10, $users); // dépend de ce que met AppFixtures
    }

    public function testUpdateUser(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(\App\Entity\User::class)->findOneBy(['username' => 'user0']);
        $user->setUsername('userUpdated' . $user->getId());
        $entityManager->flush();
        $this->assertEquals('userUpdated' . $user->getId(), $user->getUsername());
    }

    public function testDeleteUser(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(\App\Entity\User::class)->findOneBy(['username' => 'user0']);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->assertNull($entityManager->getRepository(\App\Entity\User::class)->findOneBy(['username' => 'user0']));
    }
}
