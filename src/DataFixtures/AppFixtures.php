<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = [];
        for ($i=0; $i < 10; $i++) { 
            $category = (new Category())->setName($faker->word())
                                        ->setDescription($faker->sentence(3))
                                        ->setImage($faker->imageUrl(200, 200, 'category', true))
            ;
            $categories[] = $category;
            $manager->persist($category);
        }

        $products = [];
        for ($i=0; $i < 25; $i++) { 
            $product = (new Product())->setName($faker->sentence(3))
                                      ->setDescription($faker->text(50))
                                      ->setPrice($faker->randomFloat(2, 1, 100))
                                      ->setCreatedAt($faker->dateTime())
                                      ->setImage($faker->imageUrl(640, 480, 'product', true))
                                      ->setCategory($categories[rand(0, count($categories)-1)])
            ;
            $products[] = $product;
            $manager->persist($product);
        }

        $manager->flush();
    }
}
