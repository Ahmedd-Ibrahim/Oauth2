<?php
namespace Ahmedd\Oauth2;

use GuzzleHttp\Client as BaseClient;

abstract class Oauth{

    /**
     * base url to make oauth
     */
    public static string $authTokenUrl;

    /**
     * credential to integrate
     */
    private array $requestParams = [];

    /**
     * @var BaseClient
     */
    protected $httpClient;

    /**
     * @param request params [client secret, client id, etc]
     * @param client configration
     */
    public function __construct(array $requestParams, $clintConfig = []) {
        $this->requestParams['form_params'] = $requestParams;
        $this->httpClient = new BaseClient($clintConfig);
    }

    /**
     * get vaild access token
     *
     * @return string
     */
    public function accessToken()
    {
        return $this->isExpire($this->getExpireDateTime()) ? 
        $this->reNewAccessToken()
        : $this->getAccessToken();
    }

    /**
     * setup your oauth for from first redirect
     * 
     * @return redirect | Exception
     */
    public function saveAccessTokenAndRefrehToken(array $requestParams)
    {
        $form = ['form_params' => $requestParams];

        $response = $this->httpClient->post(Oauth::$authTokenUrl, $form);

        $response = json_decode($response->getBody());

        if (! isset($response->refresh_token) || ! isset($response->access_token)) {
            Throw new \Exception('Refresh Token OR Access Token Not Provided');
        }

        $this->storeOauthTokens(
            $response->access_token, $response->refresh_token, $response->expires_in
        );

        return $this->redirectAfterSetupAuth();
    }

    /**
     * renew access token
     * 
     * @return string || Exption
     */
    public function reNewAccessToken()
    {
        if (! $this->getRefreshToken()) {
         Throw new \Exception('Refresh Token Token Not Provided');
        }

         $response = json_decode(
             $this->httpClient->post(Oauth::$authTokenUrl, $this->getParamsWithRefreshToken())->getBody()
         );

         if (! isset($response->access_token) || ! isset($response->expires_in)) {
             Throw new \Exception('Refresh Token Token Not Provided');
         }

         $this->updateAccessTokenAndExpiresIn($response->access_token, $response->expires_in);

         return $response->access_token;
    }

    /**
     * get params with refresh token
     * 
     * @return array
     */
    protected function getParamsWithRefreshToken()
    {
        $this->requestParams['form_params'] += ['refresh_token' => $this->getRefreshToken()];
        return $this->requestParams;
    }

    /**
     * redirect after confirm auth
     * 
     * @return redirect
     */
    protected abstract function redirectAfterSetupAuth();

    /**
     * store oauth tokens
     *
     * @return void
     */
    protected abstract function storeOauthTokens(string $accessToken, string $refreshToken, $expirytime = null);

    /**
     * update access token & expires time
     *
     * @return void
     */
    protected abstract function updateAccessTokenAndExpiresIn($accessToken, $expiresIn);

    /**
     * get stored access token
     *
     * @return string | null
     */
    protected abstract function getAccessToken();

    /**
     * get stored refresh token
     *
     * @return string | null
     */
    protected abstract function getRefreshToken();

    /**
     * get stored expirytime
     *
     * @return datetime
     */
    protected abstract function getExpireDateTime();

    /**
     * determine if token expire
     *
     * @return bool
     */
    protected abstract function isExpire($expiryDateTime);
}