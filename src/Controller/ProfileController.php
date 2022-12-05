<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAvatarType;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account')]
class ProfileController extends AbstractController
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('profile/profile.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/profile/{id}/picture', name: 'app_user_picture', methods: ['GET', 'POST'])]
    public function profilePicture(Request $request, User $user, UserRepository $userRepository, string $id): Response
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

        return $this->renderForm('profile/picture.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
