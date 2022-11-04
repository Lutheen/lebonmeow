<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products
        ]);
    }

    #[Route('/product/{slug}', name: 'app_single_post')]
    public function showPost(ProductRepository $productRepository, string $slug): Response
    {
        $product = $productRepository->findOneBySlug($slug);

        return $this->render('product/show.html.twig', [
            'controller_name' => 'HomeController',
            'product' => $product
        ]);
    }
}
