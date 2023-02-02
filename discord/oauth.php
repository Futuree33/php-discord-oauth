<?php
namespace discord;

use models\user;
use models\error;
use models\tokens;

final readonly class oauth
{
    private const AUTH_URL = "https://discord.com/api/oauth2/authorize";
    private const TOKEN_URL = "https://discord.com/api/oauth2/token";
    private const REVOKE_URL = "https://discord.com/api/oauth2/token/revoke";
    private const USER_URL = "https://discord.com/api/users/@me";

    public function __construct(
      private string $client_id,
      private string $client_secret,
      private string $redirect_uri,
      private array $scopes
    ) {}

    private function mapJson(string $response, object &$class) : void
    {
        $json = json_decode($response);

        foreach ($json as $key => $value)
        {
            $class->$key = $value;
        }
    }
    private function request(string $url, array $headers = [], array $params = []) : string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (count($params) != 0)
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

        return curl_exec($curl);
    }
    public function authorize() : void
    {
        $params = [
            "client_id" => $this->client_id,
            "redirect_uri" => $this->redirect_uri,
            "response_type" => "code",
            "scope" => implode(" ", $this->scopes)
        ];

        header("Location: " . self::AUTH_URL . "?" . http_build_query($params));
    }

    public function getTokens(string $code) : error | tokens | false
    {
        $params = [
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "redirect_uri" => $this->redirect_uri,
            "grant_type" => "authorization_code",
            "code" => $code
        ];

        $response = $this->request(self::TOKEN_URL, params: $params);

        if (isset($response->error))
            return new error($response->error, $response->error_description, $response->message, $response->code);

        $tokens = new tokens();

        $this->mapJson($response, $tokens);

        return $tokens;
    }

    public function getUser(string $access_token) : error | user
    {
        $headers = [
            "Authorization: Bearer " . $access_token
        ];

        $response = $this->request(self::USER_URL, $headers);

        if (isset($response->error) || isset($response->message))
            return new error($response->error, $response->error_description);

        $user = new user();

        $this->mapJson($response, $user);

        return $user;
    }

    public function refreshTokens(string $refresh_token) : tokens | error
    {
        $params = [
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "redirect_uri" => $this->redirect_uri,
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token
        ];

        $response = $this->request(self::TOKEN_URL, params: $params);

        if (isset($response->error))
            return new error($response->error, $response->error_description);

        $tokens = new tokens();

        $this->mapJson($response, $tokens);

        return $tokens;
    }

    public function revokeTokens(string $access_token) : bool
    {
        $params = [
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "token" => $access_token
        ];

        $response = $this->request(self::REVOKE_URL, ["Content-Type: application/x-www-form-urlencoded"], $params);

        if ($response === "{}")
            return true;

        return false;
    }
}