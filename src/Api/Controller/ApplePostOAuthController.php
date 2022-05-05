<?php

/*
 * This file is part of blomstra/oauth-apple.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\OAuthApple\Api\Controller;

use Exception;
use Flarum\Forum\Auth\Registration;
use Flarum\Http\Exception\RouteNotFoundException;
use FoF\Extend\Controllers\AbstractOAuthController;
use FoF\OAuth\Errors\AuthenticationException;
use Illuminate\Support\Str;
use Laminas\Diactoros\Response\RedirectResponse;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplePostOAuthController extends AbstractOAuthController
{
    /**
     * @param ServerRequestInterface $request
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $name = 'apple';
        $providers = resolve('container')->tagged('fof-oauth.providers');

        foreach ($providers as $provider) {
            if ($provider->name() === $name) {
                if ($provider->enabled()) {
                    $this->provider = $provider;
                }

                break;
            }
        }

        if (!$this->provider) {
            throw new RouteNotFoundException();
        }

        if (!(bool) (int) $this->settings->get('fof-oauth.'.$this->getProviderName())) {
            throw new RouteNotFoundException();
        }

        try {
            $redirectUri = $this->url->to('forum')->route($this->getRouteName());
            $provider = $this->getProvider($redirectUri);

            $session = $request->getAttribute('session');

            $body = $request->getBody()->getContents();

            $b = explode('&', $body);

            $state = Str::after($b[0], 'state=');
            $code = Str::after($b[1], 'code=');

            if (!$code) {
                $authUrl = $provider->getAuthorizationUrl($this->getAuthorizationUrlOptions());
                $session->put('oauth2state', $provider->getState());

                return new RedirectResponse($authUrl.'&display=popup');
            } elseif (!$state || $state !== $session->get('oauth2state')) {
                $session->remove('oauth2state');

                throw new Exception('Invalid state');
            }

            $token = $provider->getAccessToken('authorization_code', compact('code'));
            $user = $provider->getResourceOwner($token);

            return $this->response->make(
                $this->getProviderName(),
                $this->getIdentifier($user),
                function (Registration $registration) use ($user, $token) {
                    $this->setSuggestions($registration, $user, $token);
                }
            );
        } catch (Exception $e) {
            if ($e->getMessage() === 'Invalid state' || $e instanceof IdentityProviderException) {
                throw new AuthenticationException($e->getMessage());
            }

            throw $e;
        }
    }

    protected function getRouteName(): string
    {
        return 'auth.'.$this->getProviderName();
    }

    protected function getIdentifier($user): string
    {
        return $user->getId();
    }

    protected function getProvider(string $redirectUri): AbstractProvider
    {
        return $this->provider->provider(
            $this->url->to('forum')->route(
                'fof-oauth',
                ['provider' => $this->getProviderName()]
            )
        );
    }

    protected function getProviderName(): string
    {
        return $this->provider->name();
    }

    protected function getAuthorizationUrlOptions(): array
    {
        return $this->provider->options();
    }

    protected function setSuggestions(Registration $registration, $user, string $token)
    {
        $this->provider->suggestions($registration, $user, $token);
    }
}
