<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Service\FileUploader;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setSeller($this->getUser());
            
            $images = [
                $form->get('firstImage')->getData(),
                $form->get('secondImage')->getData(),
                $form->get('thirdImage')->getData(),
            ];

            foreach ($images as $imageFile) {
                if ($imageFile) {
                    $imageFileName = $this->fileUploader->upload($imageFile);
                    $image = new Image();
                    $image->setImage($imageFileName);
                    $product->addImage($image);
                }
            }

            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_product_show', methods: ['GET'])]
    public function show(ProductRepository $productRepository, string $slug): Response
    {
        $product = $productRepository->findOneBySlug($slug);
        
        return $this->render('product/show.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = [
                $form->get('firstImage')->getData(),
                $form->get('secondImage')->getData(),
                $form->get('thirdImage')->getData(),
            ];

            foreach ($images as $imageFile) {
                if ($imageFile) {
                    $imageFileName = $this->fileUploader->update($imageFile, $product->getImages());
                    $image = new Image();
                    $image->setImage($imageFileName);
                    $product->addImage($image);
                }
            }

            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
