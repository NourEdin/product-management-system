<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class JwtInternalAuthenticator extends JWTAuthenticator
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $eventDispatcher,
        TokenExtractorInterface $tokenExtractor,
        UserProviderInterface $userProvider,
        TranslatorInterface $translator = null)
    {
        parent::__construct($jwtManager, $eventDispatcher, $tokenExtractor, $userProvider, $translator);
    }
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return  $request->headers->has('Authorization');
    }

    public function doAuthenticate(Request $request)
    {
        $token = $this->getTokenExtractor()->extract($request);
        if ($token === false) {
            throw new \LogicException('Unable to extract a JWT token from the request. Also, make sure to call `supports()` before `authenticate()` to get a proper client error.');
        }

        try {
            if (!$payload = $this->jwtManager->parse($token)) {
                throw new InvalidTokenException('Invalid JWT Token');
            }
        } catch (JWTDecodeFailureException $e) {
            if (JWTDecodeFailureException::EXPIRED_TOKEN === $e->getReason()) {
                throw new ExpiredTokenException();
            }

            throw new InvalidTokenException('Invalid JWT Token', 0, $e);
        }
        //Check whether the provided token was generated for an external api
        $isExternal = $payload['isExternal'] ?? null;

        if ($isExternal) {
            throw new InvalidTokenException('Invalid JWT Token', 0);
        }

        $idClaim = $this->jwtManager->getUserIdClaim();
        if (!isset($payload[$idClaim])) {
            throw new InvalidPayloadException($idClaim);
        }

        $passport = new SelfValidatingPassport(
            new UserBadge(
                (string)$payload[$idClaim],
                function ($userIdentifier) use ($payload) {
                    return $this->loadUser($payload, $userIdentifier);
                }
            )
        );

        $passport->setAttribute('payload', $payload);
        $passport->setAttribute('token', $token);

        return $passport;
    }


}