<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $products = $productRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function header(CategoryRepository $categoryRepository, RequestStack $requestStack): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('partials/header.html.twig', [
            'categories' => $categories,
            'route' => $requestStack->getParentRequest()->getPathInfo()
        ]);
    }
}