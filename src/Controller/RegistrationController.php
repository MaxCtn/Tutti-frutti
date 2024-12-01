<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Contrôleur gérant l'inscription des utilisateurs.
 */
class RegistrationController extends AbstractController
{
    /**
     * Affiche le formulaire d'inscription et traite les données soumises.
     *
     * @Route("/registration", name="app_registration", methods={"GET", "POST"})
     *
     * @param Request                      $request           La requête HTTP.
     * @param EntityManagerInterface       $entityManager     Le gestionnaire d'entités.
     * @param UserPasswordHasherInterface  $passwordHasher    Le service de hashage de mot de passe.
     * @param UserAuthenticatorInterface   $userAuthenticator Le service d'authentification utilisateur.
     * @param SluggerInterface             $slugger           Le service de slugification.
     * @param AppCustomAuthenticator       $authenticator     Votre authentificateur personnalisé.
     *
     * @return Response La réponse HTTP.
     */
    #[Route('/registration', name: 'app_registration', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        SluggerInterface $slugger,
        AppCustomAuthenticator $authenticator
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
                    $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                    $user->setPassword($hashedPassword);

                    // Gestion de l'upload de la photo de profil
                    $this->handleProfilePicture($form->get('profilePicture')->getData(), $user, $slugger);

                    // Sauvegarder l'utilisateur dans la base
                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Authentifier automatiquement l'utilisateur
                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
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
     *
     * @param string                $email         L'adresse e-mail à vérifier.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     *
     * @return bool True si l'e-mail est déjà utilisé, false sinon.
     */
    private function isEmailAlreadyUsed(string $email, EntityManagerInterface $entityManager): bool
    {
        return (bool) $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /**
     * Gère l'upload de la photo de profil.
     *
     * @param UploadedFile|null $profilePictureFile Le fichier de la photo de profil.
     * @param User              $user               L'utilisateur en cours d'inscription.
     * @param SluggerInterface  $slugger            Le service de slugification.
     *
     * @throws \RuntimeException Si une erreur survient lors de l'upload.
     */
    private function handleProfilePicture($profilePictureFile, User $user, SluggerInterface $slugger): void
    {
        if ($profilePictureFile) {
            $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $profilePictureFile->guessExtension();

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
}
