<?php

/*
 * This file is part of blomstra/oauth-apple.
 *
 * Copyright (c) 2022 Team Blomstra.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\OAuthApple;

use Blomstra\OAuthApple\Providers\Apple;
use Flarum\Extend;
use FoF\OAuth\Extend as OAuthExtend;

// $leeway is needed for clock skew
\Firebase\JWT\JWT::$leeway = 60;

return [
    (new Extend\Frontend('forum'))
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    (new OAuthExtend\RegisterProvider(Apple::class)),
];
