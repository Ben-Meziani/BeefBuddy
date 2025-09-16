<?php

namespace App\Tests;

use App\DataFixtures\FighterFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FighterRepositoryTest extends KernelTestCase
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
            FighterFixtures::class,
        ]);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $fighters = $entityManager->getRepository(\App\Entity\Fighter::class)->findAll();
        $this->assertCount(10, $fighters);
    }

    public function testUpdateFighter(): void
    {
        $this->databaseTool->loadFixtures([
            FighterFixtures::class,
        ]);
    }

    // public function testDeleteFighter(): void
    // {
    //     $this->databaseTool->loadFixtures([
    //         FighterFixtures::class,
    //     ]);
    // }

}
