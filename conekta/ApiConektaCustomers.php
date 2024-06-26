<?php

class ApiConektaCustomers
{
    private $client;
    private $url;
    private $bearerToken;

    public function __construct()
    {
        $this->client = new GuzzleHttp\Client();
        $this->url = ConfigApiConekta::$url;
        $this->bearerToken = ConfigApiConekta::$bearerToken;
    }

    /**
     * @throws Exception
     */
    public function createCustomer($firstName, $email, $phone): string
    {
        $headers = [
            'content-type' => 'application/x-www-form-urlencoded',
            'accept' => 'application/vnd.conekta-v2.1.0+json',
            'Authorization' => 'Bearer ' . $this->bearerToken
        ];

        $options = [
            'form_params' => [
                'name' => $firstName,
                'email' => $email,
                'phone' => $phone
            ]
        ];

        $request = new GuzzleHttp\Psr7\Request('POST', "{$this->url}/customers", $headers);

        try {
            $response = $this->client->send($request, $options);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            throw new Exception('Error al registrar en conekta');
        }

        return json_decode($response->getBody())->id;
    }

    /**
     * @throws Exception
     */
    public function updateCustomer($otherPhone, $firstName, $email, $phone)
    {
        $headers = [
            'content-type' => 'application/x-www-form-urlencoded',
            'accept' => 'application/vnd.conekta-v2.1.0+json',
            'Authorization' => 'Bearer ' . $this->bearerToken
        ];

        $options = [
            'form_params' => [
                'name' => $firstName,
                'email' => $email,
                'phone' => $phone
            ]
        ];

        $request = new GuzzleHttp\Psr7\Request('PUT', "{$this->url}/customers/{$otherPhone}", $headers);

        try {
            $response = $this->client->send($request, $options);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            throw new Exception('Error al actualizar en conekta');
        }
    }

    public function customerExist($otherPhone): bool
    {
        $headers = [
            'content-type' => 'application/json',
            'accept' => 'application/vnd.conekta-v2.1.0+json',
            'Authorization' => 'Bearer ' . $this->bearerToken
        ];

        $request = new GuzzleHttp\Psr7\Request('GET', "{$this->url}/customers/{$otherPhone}", $headers);

        try {
            $response = $this->client->send($request);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        return true;
    }
}