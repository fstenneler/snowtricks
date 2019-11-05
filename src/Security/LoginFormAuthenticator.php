<?php

namespace App\Security;

use session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;
    use ContainerAwareTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $session;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session,
        Security $security
    )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
        $this->security = $security;
    }

    /**
     * first method called, check if the login route was called with post method
     *
     * @param Request $request
     * @return boolean
     */
    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Second method called, if supports() method return true
     * Store post fields into an array named $credentials and store in session last userName posted
     *
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'userName' => $request->request->get('userName'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['userName']
        );

        return $credentials;
    }

    /**
     * third method called
     * check the posted token validity, and search the userName in database
     * if the token is correct and the user was found, return the created User object
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return User
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['userName' => $credentials['userName']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('User name could not be found');
        }
        
        if(!$user->getActivated()) {
            // fail activation with a custom error
            $errorMessage = 'This user account has not been activated, please check your mailbox. If you didn\'t receive the mail, please click ';
            $errorMessage .= '<a href="' . $this->urlGenerator->generate('app_resend_activation_token', ['userName' => $credentials['userName']]) . '">here</a>';
            $errorMessage .= ' to send it again.';
            throw new CustomUserMessageAuthenticationException($errorMessage);
        }
        return $user;
    }

    /**
     * fourth method called
     * check if the password posted is the same than the user password
     *
     * @param array $credentials
     * @param UserInterface $user
     * @return boolean
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * fifth method called
     * if the checkCredentials() method return true, if a target path was stored into session, redirect to this route
     * else redirect to the manage_account route
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if($targetPath = $this->session->get('referer')) {
            return new RedirectResponse($targetPath);
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        $request->getSession()->getFlashBag()->add('success', 'You are now connected !' );
        return new RedirectResponse($this->urlGenerator->generate('manage_account'));
    }

    /**
     * return the app_login route url
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
<<<<<<< HEAD
=======

    public function logUser($user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('session')->set('_security_main', serialize($token));
    }
>>>>>>> 298483bc2ca9dac2dd5518824e2e3b89ceee8485
    
}
