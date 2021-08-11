<?php

namespace AcMarche\Duobac\Security;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;
use Symfony\Component\Security\Http\EventListener\UserProviderListener;

class DuobacBadge implements BadgeInterface
{
    private string $userIdentifier;
    /**
     * @var callable|null
     */
    private $userLoader;
    /**
     * @var callable|null
     */
    private $duobacLoader;
    private $user;
    private string $puce;
    private ?Duobac $duobac;

    public function __construct(
        string $userIdentifier,
        string $puce,
        callable $duobacLoader = null,
        callable $userLoader = null
    ) {
        $this->userIdentifier = $userIdentifier;
        $this->userLoader = $userLoader;
        $this->puce = $puce;
        $this->duobacLoader = $duobacLoader;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getPuce(): string
    {
        return $this->puce;
    }

    /**
     * @throws AuthenticationException when the user cannot be found
     */
    public function getDuobac(): ?Duobac
    {
        if (null === $this->duobac) {
            if (null === $this->duobacLoader) {
                throw new \LogicException(
                    sprintf(
                        'No user loader is configured, did you forget to register the "%s" listener?',
                        UserProviderListener::class
                    )
                );
            }

            $this->duobac = ($this->duobacLoader)($this->userIdentifier, $this->getPuce());
            if ($this->duobac === null) {
                // fail authentication with a custom error
                throw new CustomUserMessageAuthenticationException('Aucun duobac trouvé.');
            }

            //    throw new BadCredentialsException('Pas de duobac trouvé.');
            if (!$this->duobac instanceof Duobac) {
                throw new AuthenticationServiceException(
                    sprintf(
                        'The user provider must return a UserInterface object, "%s" given.',
                        get_debug_type($this->duobac)
                    )
                );
            }
        }

        return $this->duobac;
    }

    /**
     * @throws AuthenticationException when the user cannot be found
     */
    public function getUser(): ?UserInterface
    {
        if (null === $this->user) {
            if (null === $this->userLoader) {
                throw new \LogicException(
                    sprintf(
                        'No user loader is configured, did you forget to register the "%s" listener?',
                        UserProviderListener::class
                    )
                );
            }

            $this->user = ($this->userLoader)($this->userIdentifier);
            if ($this->user === null) {
                $this->user = $this->newFromDuobac($this->duobac);
            }
        }

        return $this->user;
    }

    private function newFromDuobac(Duobac $duobac): User
    {
        $user = new User();
        $user->setRdvMatricule($duobac->getRdvMatricule());
        $user->setNom($duobac->getRdvNom());
        $user->setPrenom($duobac->getRdvPrenom1());
        if (!in_array(SecurityData::getRoleUser(), $user->getRoles())) {
            $user->setRoles([SecurityData::getRoleUser()]);
        }
        $this->passwordManager->generateNewPassword($user);
        $this->passwordManager->changePassword($user, $user->getPlainPassword());
        $user->addDuobac($duobac);
        $this->userLoader->persist($user);
        $this->userLoader->flush();

        return $user;
    }

    public function getUserLoader(): ?callable
    {
        return $this->userLoader;
    }

    public function setUserLoader(callable $userLoader): void
    {
        $this->userLoader = $userLoader;
    }

    public function getDuobacLoader(): ?callable
    {
        return $this->duobacLoader;
    }

    public function setDuobacLoader(callable $duobacLoader): void
    {
        $this->duobacLoader = $duobacLoader;
    }

    public function isResolved(): bool
    {
        return true;
    }

}
