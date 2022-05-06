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
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use League\Flysystem\Adapter\Local;
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

    public function priority(): int
    {
        return 100;
    }

    public function provider(string $redirectUri): AbstractProvider
    {
        return $this->provider = new AppleProvider([
            'clientId'     => $this->getSetting('client_id'),
            'teamId'       => $this->getSetting('team_id'),
            'keyFileId'    => $this->getSetting('key_file_id'),
            'keyFilePath'  => $this->getKeyFilePath(),
            'redirectUri'  => $redirectUri,
        ]);
    }

    public function suggestions(Registration $registration, $user, string $token)
    {
        $email = $user->getEmail();

        if (!empty($email)) {
            $this->verifyEmail($email);

            $registration
            ->provideTrustedEmail($email)
            ->setPayload($user->toArray());
        } else {
            $registration
            ->setPayload($user->toArray());
        }
    }

    public function options(): array
    {
        return ['scope' => ['email']];
    }

    /**
     * The Apple provider expects a path to a key file. Depending on if the Filesystem is local or remote, we need to
     * either return the local path, or create a temporary file and return the path to that file.
     *
     * @return string
     */
    private function getKeyFilePath(): string
    {
        $keyFileSetting = $this->getSetting('key_file_path');

        if (empty($keyFileSetting)) {
            throw new \Exception('Apple key file path is not set.');
        }

        /** @var Paths $paths */
        $paths = resolve(Paths::class);

        /** @var Factory $filesystemFactory */
        $filesystemFactory = resolve(Factory::class);

        /** @var Filesystem $filesystem */
        $filesystem = $filesystemFactory->disk('apple-keyfile');

        // If we're using local storage, we can just use the key file directly.
        if ($filesystem instanceof Local) {
            if (file_exists($localPath = "$paths->storage/oauth/applekey/".$keyFileSetting)) {
                return $localPath;
            }

            throw new \Exception('Apple key file does not exist.');
        }

        // Otherwise, we'll have to fetch it from the Filesystem adapter.
        $keyfileContents = $filesystem->get($keyFileSetting);
        $tmpPath = "$paths->storage/tmp/applekey.txt";
        file_put_contents($tmpPath, $keyfileContents);

        return $tmpPath;
    }
}
