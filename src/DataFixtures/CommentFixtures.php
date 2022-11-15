<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Painting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $painting = $manager->getRepository(Painting::class)->findAll();
        $countPaint = count($painting);
        $faker = Factory::create();
        for ($i = 1; $i <= 150; $i++) {
            $comment = new Comment();
            $comment->setPseudo($faker->firstName($faker->randomElements(['male','female'])))
                    ->setPainting($painting[$faker->numberBetween(0,$countPaint - 1)])
                    ->setComment($faker->paragraph(2,true))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setIsPublished($faker->boolean(60));
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PaintingFixtures::class
        ];
    }
}
