<?php

namespace App\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTManagerDao
{
    private string $secretKey;
    private string $algorithm = 'HS256';
    private int $defaultExpiry = 3600; // 60 minutos

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Genera un token JWT
     */
    public function generateToken(int $userId, ?int $expiry = null): string
    {
        $expiryTime = time() + ($expiry ?? $this->defaultExpiry);

        $payload = [
            'exp' => $expiryTime,
            'iat' => time(),
            'iss' => $this->getIssuer(),
            'data' => [
                'userId' => $userId
            ]
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Establece la cookie de autenticación
     */
    public function setAuthCookie(string $token, ?int $expiry = null): void
    {
        $expiryTime = time() + ($expiry ?? $this->defaultExpiry);

        setcookie('auth_token', $token, [
            'expires' => $expiryTime,
            'path' => '/',
            'domain' => $this->getDomain(),
            'secure' => $this->isSecure(),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    /**
     * Valida un token JWT
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array)$decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getIssuer(): string
    {
        return ($this->isSecure() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    }

    private function getDomain(): string
    {
        return $_SERVER['HTTP_HOST'] ?? 'localhost';
    }

    private function isSecure(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }
}
