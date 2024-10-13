<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Mockery\Exception;

class OllamaClient
{

    private string $host = 'http://127.0.0.1:11434';
    public function create(array $params)
    {
        $model ??= $params['model'] ?? 'llama3.2:3b-instruct-q8_0';
        $messages = $params['messages'] or throw new Exception('No messages');

        $client = new Client();
        $response = $client->post($this->host.'/api/chat',[
            'body' => json_encode($params)
        ]);

        dd($response->getBody()->getContents());//vla
    }
}
