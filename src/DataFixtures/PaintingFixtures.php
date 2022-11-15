<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Painting;
use App\Entity\Technical;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PaintingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $category = $manager->getRepository(Category::class)->findAll();
        $countCat = count($category);
        $technical = $manager->getRepository(Technical::class)->findAll();
        $countTech = count($technical);
        $faker = Faker\Factory::create();
        $slugify = new Slugify();
        for ($i = 1; $i <= 50; $i++) {
            $paint = new Painting();
            $title = $faker->words($faker->numberBetween(2,4),true);
            $height = $faker->numberBetween(40,60);
            $cat = $category[$faker->numberBetween(0,$countCat - 1)];
            $paint->setTitle($title)
                  ->setAuthor($faker->firstName($faker->randomElements(['male','female'])). ' ' . $faker->lastName())
                  ->setMakedAt(new \DateTimeImmutable(date_format($faker->dateTimeAD(),'Y/m/d'). ' ' . $faker->time()))
                  ->setDescription($faker->paragraph(2,true))
                  ->setHeight($height)
                  ->setWidth($faker->numberBetween(20,$height - 10))
                  ->setImageName($i.'.png')
                  ->setCreatedAt(new \DateTimeImmutable())
//                  ->setIsPublished($faker->boolean(78)) // Décommentez si vous voulez un random bool
                  ->setIsPublished(true) // Commenté si vous voulez un ramdom bool
                  ->setSlug($slugify->slugify($title))
                  ->setCategory($cat)
                  ->setTechnical($technical[$faker->numberBetween(0,$countTech - 1)]);
            $manager->persist($paint);
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            TechnicalFixtures::class
        ];
    }
}
