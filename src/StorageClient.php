<?php
namespace Min\StorageClient;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class StorageClient
{
    private $redis = null;
    private $token = null;
    private $auth = null;
    private $client = null;
    private $client_id = null;
    private $secret = null;
    private $option = null;
    public function __construct()
    {
        $this->option = config('client.option');
        $this->auth = config('client.token');
        $this->client = config('client.client.uri');
        $this->client_id = config('client.client.client_id');
        $this->secret = config('client.client.client_secret');
        $this->redis = Redis::connection();
        $this->token = $this->redis->get('access_token');
        if (!$this->token) {
            $this->Oauth();
        }else{
            $expires = $this->redis->get('expires');
            $now =  Carbon::now();
            $expires_parse = Carbon::createFromTimestamp($expires);
            if($now > $expires_parse) {
                $this->redis->delete('access_token');
                $this->redis->delete('expires');
                $this->token =  null;
                $this->Oauth();
            }else{
                $this->CheckToken();
            }
        }
    }

    public function Oauth()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($this->client . $this->auth, [
            'grant_type' => "client_credentials",
            'client_id' => $this->client_id,
            'client_secret' => $this->secret,
            'scope' => 'storage',
        ]);

        $statuscode = $response->getStatusCode();
        if ($statuscode == 200) {
            $body = $response->getBody();
            $responseData = json_decode($body, true);
            $access_token = $responseData['access_token'];
            $expires_in = $responseData['expires_in'];
            $this->token = $access_token;
            $this->redis->set('access_token', $access_token);
            $expires = Carbon::now()->addMinute($expires_in / 60);
            $expires_second = $expires->diffInSeconds();
            $this->redis->expire('access_token', $expires_second);
            $this->redis->set('expires', $expires->timestamp);
            return $access_token;
        } else {
            throw new \ErrorException('res.auth.fail');
            return false;
        }
    }
    public function CheckToken()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->client . '/api/check_token');
        if(!$response->ok()){
            $this->Oauth();
        }
    }
    public function get($type, $uri, $params = null, $query = null)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->client . $this->option[$type][$uri] . $params)
            ->withQueryParams($query);
        return $this->handleResponse($response);
    }
    public function post($type, $uri, $body = null, $params = null)
    {
        if ($uri == 'upload' || $uri == 'update') {
            $file = stream_get_contents($body['file']);
            $name = explode('/', $file)[1];
            $path = $body['path'];
            $bodyData = [
                'path' => $path,
                'file' => $file,
            ];
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->attach('file', $file, $name)
              ->post($this->client . $this->option[$type][$uri] . $params, $bodyData);
        } else {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->Oauth(),
            ])
            ->withBody($body)
            ->post($this->client . $this->option[$type][$uri] . $params);
        }
        return $this->handleResponse($response);
    }
    public function put($path, $name = null, $type, $uri, $data = null, $params = null)
    {

        if ($uri == 'upload' || $uri == 'update') {
            $file = stream_get_contents($data['file']);
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->attach('file', $file, $name)->put($this->client . $this->option[$type][$uri] . $params);
        } else {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->put($this->client . $this->option[$type][$uri], $data);
        }
        return $this->handleResponse($response);
    }
    public function delete($type, $uri, $params = null, $query = null)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ])->delete($this->client . $this->option[$type][$uri] . $params);
        return $this->handleResponse($response);
    }
    protected function handleResponse(Response $response)
    {
        if ($response->ok()) {
            return $response->json();
        }
        $this->token = null;
        throw new \Exception($response->body());
    }

}
