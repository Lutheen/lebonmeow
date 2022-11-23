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
        for ($i=0; $i < 50; $i++) { 
            $user = (new User())->setUsername($faker->userName())
                                ->setEmail($faker->email())
                                ->setPassword(sha1('password'))
                                ->setIsVerified(true)
                                ->setImage($faker->imageUrl(100,100, 'user', true))
            ;
            $users[] = $user;
            $manager->persist($user);
        }

        $categories = [
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
        for ($i=0; $i < count($categories); $i++) { 
            $category = (new Category())->setName($categories[$i])
                                        ->setDescription("Description de :".$categories[$i])
                                        ->setImage($faker->imageUrl(200, 200, $categories[$i], true))
            ;
            $manager->persist($category);
        }

        // $categories = [];
        // for ($i=0; $i < 10; $i++) { 
        //     $category = (new Category())->setName($faker->word())
        //                                 ->setDescription($faker->sentence(3))
        //                                 ->setImage($faker->imageUrl(200, 200, 'category', true))
        //     ;
        //     $categories[] = $category;
        //     $manager->persist($category);
        // }

        $images = [];
        $products = [];
        for ($i=0; $i < 25; $i++) { 
            $product = (new Product())->setName($faker->sentence(3))
                                      ->setDescription($faker->text(50))
                                      ->setPrice($faker->randomFloat(2, 1, 100))
                                      ->setCreatedAt($faker->dateTime())
                                      ->addImage($images[rand(0, count($images)-1)])
                                      ->setCategory($categories[rand(0, count($categories)-1)])
                                      ->setSeller($users[rand(0, count($users)-1)])
            ;
            $products[] = $product;
            $manager->persist($product);
        }

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
