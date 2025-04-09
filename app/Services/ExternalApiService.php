<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExternalApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.external_api.url');
    }

    public function getRefreshToken($refreshToken, $clientType = 'iposClerk')
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.ipos.app/api/v1/auth/refresh-token', [
            'refreshToken' => $refreshToken,
            'clientType' => $clientType
        ]);
    }

    public function getCurrentUser($token)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://api.ipos.app/api/v1/auth/users/current-user');
    }

    public function getCompanyDetails($token, $companyId)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("https://api.ipos.app/api/v1/business/companies/{$companyId}");
    }
}