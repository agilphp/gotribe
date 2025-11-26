<?php
namespace Trekly\Auth\Infrastructure\Security;

use Trekly\Auth\Domain\User\User;
use Trekly\Auth\Domain\User\UserRepository;
use Trekly\Auth\Infrastructure\Security\JwtTokenProvider;

class GoogleOAuthService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private UserRepository $userRepository;
    private JwtTokenProvider $jwtProvider;

    public function __construct(UserRepository $userRepository, JwtTokenProvider $jwtProvider)
    {
        $this->clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
        $this->clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
        $this->redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? '';
        $this->userRepository = $userRepository;
        $this->jwtProvider = $jwtProvider;
    }

    public function getAuthUrl(): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ]);
        return "https://accounts.google.com/o/oauth2/v2/auth?$params";
    }

    /**
     * Handles the callback after Google redirects back with a code.
     * Returns an array with 'token' (JWT) and 'user' (user data).
     */
    public function handleCallback(string $code): array
    {
        // Exchange code for access token
        $tokenResponse = $this->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ]);
        $tokenData = json_decode($tokenResponse, true);
        if (!isset($tokenData['id_token'])) {
            throw new \RuntimeException('Failed to obtain id_token from Google');
        }
        // Decode ID token (JWT) payload (no verification for brevity)
        $parts = explode('.', $tokenData['id_token']);
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        $email = $payload['email'] ?? null;
        if (!$email) {
            throw new \RuntimeException('Google account email not found');
        }
        // Find or create user (role MEMBER by default)
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            // Create a dummy password (random) because password is required
            $dummyPassword = bin2hex(random_bytes(8));
            $user = User::create($email, $dummyPassword, 'MEMBER');
            $this->userRepository->save($user);
        }
        // Generate JWT for our API
        $jwt = $this->jwtProvider->createToken($user);
        return ['token' => $jwt, 'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->value
        ]];
    }

    private function post(string $url, array $data): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}
?>
