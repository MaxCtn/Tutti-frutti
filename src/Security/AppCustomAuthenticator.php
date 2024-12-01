<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authentificateur personnalisé pour gérer l'authentification des utilisateurs.
 */
class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * Constructeur de l'authentificateur.
     *
     * @param UrlGeneratorInterface $urlGenerator Générateur d'URL pour les redirections.
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Authentifie une requête utilisateur.
     *
     * @param Request $request La requête HTTP.
     * @return Passport Un objet Passport contenant les informations d'identification et les badges.
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        // Stocke l'email saisi dans la session pour pré-remplir le champ en cas d'erreur.
        $request->getSession()->set('_security.last_username', $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Gère la redirection après une authentification réussie.
     *
     * @param Request        $request       La requête HTTP.
     * @param TokenInterface $token         Le token d'authentification.
     * @param string         $firewallName  Le nom du pare-feu.
     * @return Response|null Une réponse HTTP ou null.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirection par défaut après authentification réussie
        return new RedirectResponse($this->urlGenerator->generate('album_search'));
    }

    /**
     * Renvoie l'URL de connexion.
     *
     * @param Request $request La requête HTTP.
     * @return string L'URL de la page de connexion.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
