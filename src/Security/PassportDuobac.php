<?php

namespace AcMarche\Duobac\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportTrait;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class PassportDuobac implements UserPassportInterface
{
    use PassportTrait;

    private $user;

    /**
     * @param CredentialsInterface $credentials the credentials to check for this authentication, use
     *                                          SelfValidatingPassport if no credentials should be checked
     * @param BadgeInterface[] $badges
     */
    public function __construct(DuobacBadge $duobacBadge, CredentialsInterface $credentials, array $badges = [])
    {
        $this->addBadge($duobacBadge);
        $this->addBadge($credentials);
        foreach ($badges as $badge) {
            $this->addBadge($badge);
        }
    }

    public function getUser(): UserInterface
    {
        if (null === $this->user) {
            if (!$this->hasBadge(DuobacBadge::class)) {
                throw new \LogicException(
                    'Cannot get the Security user, no username or UserBadge configured for this passport.'
                );
            }

            $duobac = $this->getBadge(DuobacBadge::class)->getDuobac();
            $this->user =  $this->getBadge(DuobacBadge::class)->getUser();
        }

        return $this->user;
    }
}
