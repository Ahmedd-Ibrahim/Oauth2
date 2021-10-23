
# Oauth2 package 

### This package may help developers to handel Oauth in a simple way

## install

composer require ahmedd-ibrahim/oauth2

### Usage

```
<?php

class YourOwnClass extends Oauth
{
    public function __construct(array $requesrParam, $clientConfig = []) {
        parent::__construct($requesrParam, $clientConfig);
        Oauth::$authTokenUrl = 'https://accounts.zoho.com/oauth/v2/token?';
    }
}

```

#### Get Access Token

```
<?php

     $param = [
                 'client_id' => env('CLIENT_ID'),
                 'client_secret' => env('CLINET_SECRET'),
                 'grant_type' => 'refresh_token',
                 'redirect_uri' => env('REDIRECT_URI'),
                 'currentUserEmail' => env('EMAIL'),
        ];

return (new Oauth($param))->accessToken();
```