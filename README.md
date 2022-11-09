# Log in With Apple

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/blomstra/oauth-apple.svg)](https://packagist.org/packages/blomstra/oauth-apple) [![Total Downloads](https://img.shields.io/packagist/dt/blomstra/oauth-apple.svg)](https://packagist.org/packages/blomstra/oauth-apple)

A [Flarum](http://flarum.org) extension. Log in with Apple

## Installation

Install with composer:

```sh
composer require blomstra/oauth-apple:"*"
```

## Updating

```sh
composer update blomstra/oauth-apple
php flarum cache:clear
```

## Configuration

1. Create an `App ID` for your website (https://developer.apple.com/account/resources/identifiers/list/bundleId) with the following details:
    - Platform: iOS, tvOS, watchOS (I'm unsure if either choice has an effect for web apps)
    - Description: (something like "example.com app id")
    - Bundle ID (Explicit): com.example.id (or something similar)
    - Check "Sign In With Apple"
2. Create a `Service ID` for your website (https://developer.apple.com/account/resources/identifiers/list/serviceId) with the following details:
    - Description: (something like "example.com service id")
    - Identifier: com.example.service (or something similar)
    - Check "Sign In With Apple"
    - Configure "Sign In With Apple":
        - Primary App Id: (select the primary app id created in step 1)
        - Web Domain: example.com (the domain of your web site)
        - Return URLs: https://example.com/apple-signin (the route pointing to the callback method in your controller)
        - Click "Save".
        - Click the "Edit" button to edit the details of the "Sign In With Apple"
            configuration we just created.
        - If you haven't verified the domain yet, download the verification file,
            upload it to https://example.com/.well-known/apple-developer-domain-association.txt, and then click the "Verify"
            button.
3. Create a `Private Key` for your website (https://developer.apple.com/account/resources/authkeys/list) with the following details:
    - Key Name: 
    - Check "Sign In With Apple"
    - Configure "Sign In With Apple":
        - Primary App ID: (select the primary app id created in step 1)
        - Click "Save"
    - Click "Continue"
    - Click "Register"
    - Click "Download"
    - Keep the file safe for use in step 4.

4. Fill in the following fields in the extension settings:
    - `team_id`: This can be found on the top-right corner when logged into
            your Apple Developer account, right under your name.
    - `client_id`: This is the identifier from the Service Id created in step
            2 above, for example com.example.service
    - `key_id`: This is the identifier of the private key created in step 3
            above.
    - Upload the key file downloaded in step 3.

## Sponsored

The initial version of this extension was kindly sponsored by [Kagi Search](https://kagi.com) - an ad-free search engine
## Links

- [Packagist](https://packagist.org/packages/blomstra/oauth-apple)
- [GitHub](https://github.com/blomstra/flarum-ext-oauth-apple)
- [Discuss](https://discuss.flarum.org/d/31938-blomstra-sign-in-with-apple)
