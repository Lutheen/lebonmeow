<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgot-pwd', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On va chercher l'utilisateur par son email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un utilisateur
            if ($user) {
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On crée les données du mail
                $context = compact('url', 'user');

                // Envoi du mail

                $mail = (new TemplatedEmail())
                    ->from(new Address('contact@lebonmeow.com', '\"Contact Lebonmeow\"'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de mot de passe')
                    ->htmlTemplate('security/reset_password_email.html.twig')
                    ->context($context)
                ;

                $mailer->send($mail);

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }

            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPwdForm' => $form->createView()
        ]);
    }

    #[Route(path: '/reset-pwd/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // On vérifie si on a ce token dans la base
        $user = $userRepository->findOneByResetToken($token);

        // On vérifie si l'utilisateur existe
        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                // On efface le token
                $user->setResetToken('');

                // On enregistre le nouveau mot de passe en le hashant
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'pwdForm' => $form->createView()
            ]);
        }

        // Si le token est invalide on redirige vers le login
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
