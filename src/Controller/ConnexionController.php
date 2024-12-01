<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant les actions de connexion et de déconnexion des utilisateurs.
 */
class ConnexionController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion et traite les tentatives de connexion.
     *
     * @Route("/login", name="app_login", methods={"GET", "POST"})
     *
     * @param AuthenticationUtils $authenticationUtils Utilitaire d'authentification pour récupérer les erreurs et le dernier nom d'utilisateur saisi.
     *
     * @return Response La réponse HTTP contenant le formulaire de connexion.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer l'e-mail à partir de la session
        $lastEmail = $authenticationUtils->getLastUsername();

        // Récupérer l'erreur de connexion, le cas échéant
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error
        ]);
    }

    /**
     * Point de déconnexion de l'application.
     *
     * @Route("/logout", name="app_logout", methods={"GET"})
     *
     * @throws \LogicException Cette méthode est interceptée par le pare-feu de sécurité et ne devrait pas être exécutée.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut être laissée vide, car elle est interceptée par le pare-feu de sécurité
        throw new \LogicException('Cette méthode peut être vide - elle sera interceptée par la clé logout de votre pare-feu.');
    }
}
