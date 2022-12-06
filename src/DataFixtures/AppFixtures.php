<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $users = [];
        for ($i=0; $i < 10; $i++) { 
            $user = (new User())->setUsername($faker->userName())
                                ->setEmail($faker->email())
                                ->setPassword(sha1('password'))
                                ->setIsVerified(true)
                                ->setImage($faker->imageUrl(100,100, 'user', true))
                                ->setCreatedAt($faker->dateTime())
            ;
            $users[] = $user;
            $manager->persist($user);
        }

        $names = [
            "Immobilier",
            "Véhicules",
            "Vacances",
            "Emploi",
            "Mode",
            "Maison",
            "Multimédia",
            "Loisirs",
            "Matériel professionnel",
            "Autres"
        ];
        $categories = [];
        for ($i=0; $i < count($names); $i++) { 
            $category = (new Category())->setName($names[$i])
                                        ->setDescription("Description de :".$names[$i])
                                        ->setImage($faker->imageUrl(200, 200, $names[$i], true))
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
                                      ->setCategory($categories[rand(0, count($categories)-1)])
                                      ->setSeller($users[rand(0, count($users)-1)])
            ;
            $products[] = $product;
            $manager->persist($product);

        }

        $images = [];
        for ($i=0; $i < 50; $i++) {
            $image = (new Image())->setImage($faker->imageUrl(700, 500, 'image', true))
                                  ->setProduct($products[rand(0, count($products)-1)])
            ;
            $images[] = $image;
            $manager->persist($image);
        }

        $manager->flush();
    }
}
