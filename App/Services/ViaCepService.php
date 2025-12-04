<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class ViaCepService {
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://viacep.com.br/ws/',
            'timeout' => 5.0,
        ]);
    }

    public function buscarEndereco(string $cep): ?array
    {
        $cep = preg_replace('/[^0-9]/', '', $cep); // sanitiza o CEP

        if (strlen($cep) !== 8) {
            return null;
        }

        try {
            $response = $this->client->request('GET', "{$cep}/json/");
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['erro'])) {
                return null;
            }

            return [
                'logradouro' => $data['logradouro'] ?? '',
                'bairro' => $data['bairro'] ?? '',
                'cidade' => $data['localidade'] ?? '',
                'uf' => $data['uf'] ?? '',
                'cep' => $data['cep'] ?? '',
            ];

        } catch (GuzzleException $e) {
            return null;
        }
    }
}

?>