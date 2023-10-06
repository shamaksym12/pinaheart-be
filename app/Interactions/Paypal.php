<?php
namespace App\Interactions;
use GuzzleHttp\Client;

class Paypal
{
    protected $uri;
    protected $method;
    protected $data;
    protected $client;

    public function __construct()
    {
        $this->data = [];
        $this->setClient();
    }

    public function products()
    {
        $this->uri = 'catalogs/products';
        return $this;
    }

    public function plans($params = null)
    {
        $this->uri = 'billing/plans';
        return $this;
    }

    public function subscriptions($params = null)
    {
        $this->uri = 'billing/subscriptions';
        return $this;
    }

    public function webhooks($params = null)
    {
        $this->uri = 'notifications/webhooks';
        return $this;
    }

    public function get($item = null, $params = null)
    {
        if($item) {
            $this->uri .= '/'.$item;
        }
        if($params && is_array($params)) {
            $this->uri .= '?'.http_build_query($params);
        }
        $this->method = 'GET';

        return $this->send();
    }

    public function create(array $data)
    {
        $this->method = 'POST';
        $this->data['json'] = $data;

        return $this->send();
    }

    public function delete($item = null)
    {
        if($item) {
            $this->uri .= '/'.$item;
        }
        $this->method = 'DELETE';

        return $this->send();
    }

    public function getWithParams($uri = null, $uriParams = [], $httpParams = [])
    {
        if($uri) {
            $this->uri .= '/'.$uri;
        }
        if($uriParams && is_array($uriParams)) {
            $this->uri .= '?'.http_build_query($uriParams);
        }
        if($httpParams) {
            $this->data = $httpParams;
        }
        $this->method = 'GET';
        return $this->send();
    }

    public function postWithParams($uri = null, $uriParams = [], $httpParams = [])
    {
        if($uri) {
            $this->uri .= '/'.$uri;
        }
        if($uriParams && is_array($uriParams)) {
            $this->uri .= '?'.http_build_query($uriParams);
        }
        if($httpParams) {
            $this->data['json'] = $httpParams;
            // $this->data = $httpParams;
        }
        $this->method = 'POST';
        return $this->send();
    }

    public function patchWithParams($uri = null, $uriParams = [], $httpParams = [])
    {
        if($uri) {
            $this->uri .= '/'.$uri;
        }
        if($uriParams && is_array($uriParams)) {
            $this->uri .= '?'.http_build_query($uriParams);
        }
        if($httpParams) {
            $this->data['json'] = $httpParams;
            // $this->data = $httpParams;
        }
        $this->method = 'PATCH';
        return $this->send();
    }

    protected function setClient()
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');

        if( ! $clientId && ! $clientSecret) {
            throw new \Exception("Set paypal api credentails", 1);
        }
        $params = [
            'base_uri' => $this->getBaseUrl(),
            'headers' => [
                'Accept' => 'application/json',
            ],
            'http_errors' => true,
            // 'http_errors' => false,
        ];
        $this->client = new Client($params);
        $this->method = 'POST';
        $this->uri = 'oauth2/token';
        $this->data = [
            'auth' => [$clientId, $clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ]
        ];
        $tokenData = $this->send();
        // \Log::info((array) $tokenData);
        $config = $this->client->getConfig();
        $config['headers']['Authorization'] = 'Bearer '.$tokenData->access_token;
        $this->client = new Client($config);
    }

    protected function send()
    {
        try {
            $response = $this->client->request($this->method, $this->uri, $this->data);
            return json_decode($response->getBody()->getContents());
            $this->data = [];
        } catch (\Throwable $th) {
            throw new \Exception($th->getCode().' - '.$th->getMessage(), 1);
        }
    }

    protected function getBaseUrl()
    {
        switch (config('services.paypal.env')) {
            case 'sandbox':
                return 'https://api.sandbox.paypal.com/v1/';
                break;
            case 'live':
                return 'https://api.paypal.com/v1/';
                break;
            default:
                throw new \Exception("Wrong paypal env", 1);
                break;
        }
    }
}