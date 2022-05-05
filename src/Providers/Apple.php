<?php

/*
 * This file is part of blomstra/oauth-apple.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\OAuthApple\Providers;

use Flarum\Forum\Auth\Registration;
use Flarum\Foundation\Paths;
use FoF\OAuth\Provider;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Apple as AppleProvider;

class Apple extends Provider
{
    /**
     * @var AppleProvider
     */
    protected $provider;

    public function name(): string
    {
        return 'apple';
    }

    public function link(): string
    {
        return 'https://developer.apple.com/documentation/sign_in_with_apple';
    }

    public function fields(): array
    {
        return [
            'client_id'       => 'required',
            'team_id'         => 'required',
            'key_file_id'     => 'required',
        ];
    }

    public function provider(string $redirectUri): AbstractProvider
    {
        /** @var Paths $paths */
        $paths = resolve(Paths::class);

        return $this->provider = new AppleProvider([
            'clientId'     => $this->getSetting('client_id'),
            'teamId'       => $this->getSetting('team_id'),
            'keyFileId'    => $this->getSetting('key_file_id'),
            'keyFilePath'  => "$paths->storage/oauth/applekey/".$this->getSetting('key_file_path'),
            'redirectUri'  => $redirectUri,
        ]);
    }

    public function suggestions(Registration $registration, $user, string $token)
    {
        $this->verifyEmail($email = $user->getEmail());

        $registration
            ->provideTrustedEmail($email)
            ->setPayload($user->toArray());
    }

    public function options(): array
    {
        return ['scope' => 'email'];
    }
}
