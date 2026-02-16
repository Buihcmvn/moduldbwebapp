<?php
// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    private $entityManager;
    private $passwordHasher;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'login_check' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        $passport = new Passport(
            new UserBadge($username),
            new PasswordCredentials($password)
        );

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid password.');
        }

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/home');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}