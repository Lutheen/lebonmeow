<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAddressType;
use App\Form\UserAvatarType;
use App\Form\UserFullNameType;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account')]
class AccountController extends AbstractController
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('account/profile.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/profile/{id}/picture', name: 'app_user_picture', methods: ['GET', 'POST'])]
    public function avatar(Request $request, User $user, UserRepository $userRepository, string $id): Response
    {
        $user = $userRepository->findOneById($id);

        $form = $this->createForm(UserAvatarType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('image')->getData();
            if ($avatarFile) {
                if ($user->getImage()) {
                    $avatarFilename = $this->fileUploader->updateAvatar($avatarFile, $user->getImage());
                }
                else {
                    $avatarFilename = $this->fileUploader->uploadAvatar($avatarFile);
                }
                $user->setImage($avatarFilename);
            }
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/picture.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/picture/delete', name: 'app_user_picture_delete', methods: ['POST'])]
    public function avatarDelete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getImage(), $request->request->get('_token'))) {
            $avatar = $user->getImage();

            if ($avatar) {
                $avatarName = $this->getParameter('static_directory').'/'.$avatar;
                if (file_exists($avatarName)) {
                    unlink($avatarName);
                }
                $user->setImage(null);
            }
            $userRepository->save($user, true);
        }

        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/my-products', name: 'app_user_products', methods: ['GET'])]
    public function showProducts(): Response
    {
        return $this->render('account/products.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/{id}/settings', name: 'app_user_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, User $user, UserRepository $userRepository, string $id): Response
    {
        $user = $userRepository->findOneById($id);

        $fullnameForm = $this->createForm(UserFullNameType::class, $user);
        $addressForm = $this->createForm(UserAddressType::class, $user);
        $fullnameForm->handleRequest($request);
        $addressForm->handleRequest($request);

        if ($fullnameForm->isSubmitted() && $fullnameForm->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/settings.html.twig', [
            'user' => $user,
            'fullnameForm' => $fullnameForm,
            'addressForm' => $addressForm,
        ]);
    }

    #[Route('/security', name: 'app_user_security', methods: ['GET', 'POST'])]
    public function security(Request $request, User $user, UserRepository $userRepository): Response
    {
        return $this->render('account/security.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
