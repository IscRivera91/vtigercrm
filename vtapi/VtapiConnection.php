<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
require_once dirname(__DIR__).'/config.vtapi.php';

class VtapiConnection
{
    private $client;
    private $url;
    private $username;
    private $token;
    private $accessKey;
    private $sessionName;
    private $userId;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new GuzzleHttp\Client();
        $this->username = ConfigurationVtapi::$username;
        $this->accessKey = ConfigurationVtapi::$accessKey;
        $this->url = ConfigurationVtapi::$url;

        if (!$this->generateToken()) {
            throw new Exception('Error trying to generate the Token');
        }

        if (!$this->generateSessionNameAndUserId()) {
            throw new Exception('Error trying to generate the SessionName and UserId');
        }

    }

    public function getSessionName()
    {
        return $this->sessionName;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    private function generateToken(): bool
    {
        $request = new GuzzleHttp\Psr7\Request('GET', "{$this->url}/webservice.php?operation=getchallenge&username={$this->username}");

        try {
            $response = $this->client->send($request);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        if ($this->existErrorResponse($response)) {
            return false;
        }

        $this->token = json_decode($response->getBody())->result->token;

        return true;

    }

    private function generateSessionNameAndUserId(): string
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $options = [
            'form_params' => [
                'operation' => 'login',
                'username' => $this->username,
                'accessKey' => md5($this->token.$this->accessKey),
            ]
        ];

        $request = new GuzzleHttp\Psr7\Request('POST', "{$this->url}/webservice.php", $headers);

        try {
            $response = $this->client->send($request, $options);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        if ($this->existErrorResponse($response)) {
            return false;
        }

        $this->sessionName = json_decode($response->getBody())->result->sessionName;
        $this->userId = json_decode($response->getBody())->result->userId;

        return true;
    }

    private function existErrorResponse($response): bool
    {
        if (isset(json_decode($response->getBody())->error)) {
            return true;
        }

        return false;
    }
}