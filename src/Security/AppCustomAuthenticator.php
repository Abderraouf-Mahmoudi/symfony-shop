<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private Security $security;
    
    public function __construct(private UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->security = $security;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirect to admin dashboard after login
        return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
    
    /**
     * Cette méthode est appelée quand un utilisateur non authentifié tente d'accéder à une ressource protégée
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // Nettoyer la session actuelle si elle existe pour éviter les sessions "zombies"
        if ($request->hasSession()) {
            $session = $request->getSession();
            if ($session->isStarted()) {
                $session->invalidate();
            }
        }
        
        // Créer une réponse qui va rediriger vers la page de login
        $url = $this->getLoginUrl($request);
        
        // Ajouter des en-têtes anti-cache pour s'assurer que le navigateur n'utilise pas de cache
        $response = new RedirectResponse($url);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }
    
    /**
     * Réaction en cas d'échec d'authentification
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // Nettoyer complètement la session en cas d'échec d'authentification
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
            if ($request->getSession()->isStarted()) {
                // Supprimer les données de sécurité potentielles
                $request->getSession()->remove('_security_back');
            }
        }
        
        $url = $this->getLoginUrl($request);
        
        return new RedirectResponse($url);
    }
    
    /**
     * Vérifie si cette requête support l'authentification ou non
     */
    public function supports(Request $request): bool
    {
        // Vérifie si nous sommes sur la route de login et que c'est une requête POST
        $isLoginRoute = $request->attributes->get('_route') === self::LOGIN_ROUTE;
        $isPost = $request->isMethod('POST');
        
        // Vérifie aussi que l'utilisateur n'est pas déjà authentifié pour éviter une double authentification
        $isAuthenticated = $this->security->isGranted('IS_AUTHENTICATED_FULLY');
        
        return $isLoginRoute && $isPost && !$isAuthenticated;
    }
}
