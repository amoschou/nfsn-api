<?php

namespace App\Http\Controllers\NFSN;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    protected static $object;
    protected $id;
    private static $login;
    private static $apiKey;

    protected function __construct() {
        self::$login = config('services.nfsn.login');
        self::$apiKey = config('services.nfsn.api_key');
    }

    private function getObject() {
        return self::$object;
    }

    private function getId() {
        return $this->id;
    }

    private function getLogin() {
        return self::$login;
    }

    private function getApiKey() {
        return self::$apiKey;
    }

    private static function newSalt() {
        $salt = '';

        for($i = 0; $i < 16; $i++) {
            $salt .= substr('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', random_int(0,61), 1);
        }

        return $salt;
    }

    private function wrapIfScalar($input, $key = null) {
        if(is_scalar($input)) {
            if(is_null($key)) {
                return [$input];
            }

            return [$key => $input];
        }

        return $input;
    }

    protected function request($httpMethod, $resource, $input = null, $keys = []) {
        $login = $this->getLogin();
        $timestamp = (string) now()->timestamp;
        $salt = self::newSalt();
        $apiKey = $this->getApiKey();
        $requestUri = implode('/', ['', $this->getObject(), $this->getId(), $resource]);
        $requestFullUri = implode('', [config('services.nfsn.protocol'), '://', config('services.nfsn.server_name'), $requestUri]);

        $keys = $this->wrapIfScalar($keys);

        if(count($keys) === 1) {
            $input = $this->wrapIfScalar($input, $keys[0]);
        }

        $payload = [];

        foreach($keys as $key) {
            if($input[$key] ?? false) {
                $payload[$key] = $input[$key];
            }
        }

        $body = match(Str::lower($httpMethod)) {
            'get' => '',
            'put' => (string) $input,
            'post' => http_build_query($payload),
        };

        $parameters = match(Str::lower($httpMethod)) {
            'get' => null,
            'put' => null,
            'post' => $payload,
        };

        $request = Http::withHeaders([
            'X-NFSN-Authentication' => implode(';', [
                $login, $timestamp, $salt, sha1(implode(';', [
                    $login, $timestamp, $salt, $apiKey, $requestUri, sha1($body)
                ]))
            ])
        ]);

        $response = match(Str::lower($httpMethod)) {
            'get' => $request->get($requestFullUri),
            'put' => $request->withBody($body, 'text/plain')->put($requestFullUri),
            'post' => $request->asForm()->post($requestFullUri, $parameters),
        };

        return $response;
    }
}
