<?php
namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    // generate a token
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if($validity > 0){
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
    
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // encode in base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // clean encoded value (remove +, / and =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // generate signature
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // create token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    // check if token is valid
    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    // get the header token
    public function getHeader(string $token): array
    {
        // unmount token
        $array = explode('.', $token);

        // decode header
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // get the payload token
    public function getPayload(string $token): array
    {
        // unmount token
        $array = explode('.', $token);

        // decode payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // check if token as expire
    public function isExpired(string $token): bool
    {
        // get payload's token
        $payload = $this->getPayload($token);
        // get now date to compare with payload token
        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // check token's signature
    public function check(string $token, string $secret)
    {
        // get the header & payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // regenerate token to check validity of signature
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}