# SilverStripe Google Authenticator

## Introduction

This module enables Google authentication on SilverStripe.

It works by matching the email address obtained from Google login with the email address in the SilverStripe user account list, and logs in the user if there is a match.

## Requirements

* [SilverStripe 3.1.x](https://github.com/silverstripe/silverstripe-framework)
* [Google APIs Client Library for PHP](https://github.com/google/google-api-php-client)

## Installation

### via composer

`composer require "xpointo/silverstripe-google-authenticator:dev-master"`

## Configuration

1. Obtain the Google OAuth Client ID & Client Secret by following the instructions in the [Google API Documentation](https://developers.google.com/api-client-library/php/auth/web-app)
2. For the redirect uris setting in the Google OAuth configuration, please include the URL: 
  * `http://[yoursitename]/GoogleAuthenticatorController/callback`
3. Define the Google OAuth Client ID & Client Secret in your `_ss_environment.php` or `mysite/config.php` file.
```PHP
define('GOOGLE_AUTHENTICATOR_CLIENT_ID', '[google-oauth-client-id]');
define('GOOGLE_AUTHENTICATOR_CLIENT_SECRET', '[google-oauth-client-secret]');
```


