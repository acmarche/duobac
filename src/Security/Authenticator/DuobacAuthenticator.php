<?php


namespace AcMarche\Duobac\Security\Authenticator;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\UserRepository;
use AcMarche\Duobac\Security\UserFactory;
use AcMarche\Duobac\Service\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 *
 */
class DuobacAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;
    private DuobacRepository $duobacRepository;
    private ParameterBagInterface $parameterBag;
    private UserFactory $userFactory;
    private LoggerInterface $logger;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserRepository        $userRepository,
        DuobacRepository      $duobacRepository,
        UserFactory           $userFactory,
        ParameterBagInterface $parameterBag, LoggerInterface $logger
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->duobacRepository = $duobacRepository;
        $this->parameterBag = $parameterBag;
        $this->userFactory = $userFactory;
        $this->logger = $logger;
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->getLoginUrl($request) === $request->getPathInfo();
    }

    /**
     * Dans passeport, new UserBadge($rrn) va chercher un user par son rrn
     * Si aucun user existe auth echoue
     * Donc je cree user avant si findDuobac ok
     */
    public function authenticate(Request $request): Passport
    {
        $rrn = StringUtils::removeChars($request->request->get('username', ''));
        $puce = StringUtils::removeChars($request->request->get('password', ''));
        $token = $request->request->get('_csrf_token', '');

        $duobac = $this->duobacRepository->findByRrnAndPuce($rrn, $puce);

        if ($duobac instanceof Duobac) {
            if (!$this->userRepository->loadUserByIdentifier($rrn)) {
                $this->userFactory->create($duobac);
            }
        }

        $badges =
            [
                new CsrfTokenBadge('authenticate', $token),
            ];

        $credentials = new CustomCredentials(function ($credentials, UserInterface $user): bool {
            return $user->getUserIdentifier() === $credentials;
        }, $rrn);//ici je pourrais mettre [$rrn, $puce] et y acceder via credentials[0]

        return new Passport(
            new UserBadge($rrn),
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
