<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class GenerateToken
{

    private $tokenGenerator;

    public function __construct(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Generate and save User token
     *
     * @param User $user
     * @return User
     */
    public function generate(User $user)
    {
        try {
            $token = $this->tokenGenerator->generateToken();
            $user->setToken($token);
            $user->setTokenCreationDate(new \DateTime());
        } catch (\Exception $e) {
            throw $e->getMessage();
        }
        return $user;
    }

}
