<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $products = $productRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function searchBar()
    {
        $form = $this->createForm(SearchType::class, null, [
            'action' => $this->generateUrl('app_handle_search'),
        ]);

        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/search/result', name: 'app_handle_search')]
    public function handleSearch(Request $request, ProductRepository $productRepository)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        
        $query = $form->get('query')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $productRepository->findProductsByName($query);
        }

        return $this->render('search/index.html.twig', [
            'products' => $products
        ]);
    }
}
