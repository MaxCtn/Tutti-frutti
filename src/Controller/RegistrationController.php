<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        SluggerInterface $slugger
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'utilisateur existe déjà
            if ($this->isEmailAlreadyUsed($user->getEmail(), $entityManager)) {
                $form->get('email')->addError(new FormError('Cette adresse e-mail est déjà utilisée.'));
            } else {
                try {
                    // Hashage du mot de passe
                    $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                    $user->setPassword($hashedPassword);

                    // Gestion de l'upload de la photo de profil
                    $this->handleProfilePicture($form->get('profilePicture')->getData(), $user, $slugger);

                    // Sauvegarder l'utilisateur dans la base
                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Authentifier automatiquement l'utilisateur
                    $this->authenticateUser($user, $tokenStorage, $session);

                    // Redirection après inscription réussie
                    return $this->redirectToRoute('album_search');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de votre inscription.');
                }
            }
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Vérifie si l'e-mail est déjà utilisé par un autre utilisateur.
     */
    private function isEmailAlreadyUsed(string $email, EntityManagerInterface $entityManager): bool
    {
        return (bool) $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /**
     * Gère l'upload de la photo de profil.
     */
    private function handleProfilePicture($profilePictureFile, User $user, SluggerInterface $slugger): void
    {
        if ($profilePictureFile) {
            $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

            try {
                $profilePictureFile->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFilename
                );
                $user->setProfilePicture($newFilename);
            } catch (FileException $e) {
                throw new \RuntimeException('Erreur lors de l\'upload de la photo : ' . $e->getMessage());
            }
        }
    }

    /**
     * Authentifie l'utilisateur automatiquement après inscription.
     */
    private function authenticateUser(User $user, TokenStorageInterface $tokenStorage, SessionInterface $session): void
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($token);
        $session->set('_security_main', serialize($token));
    }
}