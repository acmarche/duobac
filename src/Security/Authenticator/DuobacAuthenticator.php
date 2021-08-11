<?php


namespace AcMarche\Duobac\Security\Authenticator;

use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\UserRepository;
use AcMarche\Duobac\Security\DuobacBadge;
use AcMarche\Duobac\Security\PassportDuobac;
use AcMarche\Duobac\Service\StringUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Essayer de voir les events
 * Si reponse null en cas de failure le manager va essayer un autre authenticator
 * @see \Symfony\Component\Security\Http\Authentication\AuthenticatorManager
 * @see UserCheckerListener::postCheckCredentials
 * @see UserProviderListener::checkPassport
 * @see CheckCredentialsListener
 * bin/console debug:event-dispatcher --dispatcher=security.event_dispatcher.main
 */
class DuobacAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;
    private DuobacRepository $duobacRepository;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        DuobacRepository $duobacRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->duobacRepository = $duobacRepository;
        $this->parameterBag = $parameterBag;
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->getLoginUrl($request) === $request->getPathInfo();
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = StringUtils::removeChars($request->request->get('username', ''));
        $password = StringUtils::removeChars($request->request->get('password', ''));
        $token = $request->request->get('_csrf_token', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $badges =
            [
                new CsrfTokenBadge('authenticate', $token),
            ];

        $credentials = new CustomCredentials(function ($credentials, UserInterface $user) {
            dump($user);

            return $user->getApiToken() === $credentials;
        }, $email);

        $duobacBage = new DuobacBadge(
            $email, $password, function ($email, $password) {
            return $this->duobacRepository->loadUserByIdentifier($email, $password);
        },
            function ($email) {
                return $this->userRepository->loadUserByIdentifier($email);
            }
        );

        return new PassportDuobac(
            $duobacBage,
            $credentials,
            $badges
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('duobac_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
