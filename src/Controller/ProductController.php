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
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/product')]
class ProductController extends AbstractController
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
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

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
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
                    $imageFileName = $this->fileUploader->upload($imageFile);
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
        $images = $product->getImages();
        
        if ($images) {
            // Loop on product images
            foreach ($images as $image) {
                // Generate absolute path
               $imageName = $this->getParameter('static_directory').'/'.$image->getImage();
               // Check if image exists
               if (file_exists($imageName)) {
                    unlink($imageName); 
               }
            }
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_user_products', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image/{id}', name: 'app_product_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, Image $image, ImageRepository $imageRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            $imageName = $image->getImage();

            if (file_exists($imageName)) {
                unlink($this->getParameter('static_directory').'/'.$imageName);
            }
            $imageRepository->remove($image, true);

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }
}
