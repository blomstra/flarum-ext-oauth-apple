<?php

/*
 * This file is part of blomstra/oauth-apple.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\OAuthApple;

use Blomstra\OAuthApple\Api\Controller\ApplePostOAuthController;
use Blomstra\OAuthApple\Api\Controller\DeleteKeyFileController;
use Blomstra\OAuthApple\Api\Controller\KeyFileUploadController;
use Blomstra\OAuthApple\Providers\Apple;
use Flarum\Extend;
use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;
use FoF\OAuth\Extend as OAuthExtend;

// $leeway is needed for clock skew
\Firebase\JWT\JWT::$leeway = 60;

return [
    (new Extend\Frontend('forum'))
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Filesystem())
        ->disk('apple-keyfile', function (Paths $paths, UrlGenerator $url): array {
            return [
                'root'   => "$paths->storage/oauth/applekey",
            ];
        }),

    (new Extend\Routes('api'))
        ->post('/oauth/apple/keyfile', 'oauth.apple.keyfile.create', KeyFileUploadController::class)
        ->delete('/oauth/apple/keyfile', 'oauth.apple.keyfile.delete', DeleteKeyFileController::class),

    (new Extend\Routes('forum'))
        ->post('/auth/apple', 'oauth.apple.post', ApplePostOAuthController::class),

    (new Extend\Csrf())
        ->exemptRoute('oauth.apple.post')
        ->exemptRoute('register'),

    (new OAuthExtend\RegisterProvider(Apple::class)),
];
