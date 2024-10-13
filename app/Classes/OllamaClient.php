<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class OllamaClient
{

    private string $host = 'http://127.0.0.1:11434';
    public function create(array $params): string
    {
        $params['model'] ??= 'llama3.2:3b-instruct-q8_0';
        $messages = $params['messages'] or throw new Exception('No messages');

        $client = new Client();
        $response = $client->post($this->host.'/api/chat',[
            'body' => json_encode($params)
        ]);

        $content = $response->getBody()->getContents();
        $exploded = explode(PHP_EOL, $content);
        $to_return = "";
        foreach ($exploded as $item){
            $decoded = json_decode($item, true, flags: JSON_INVALID_UTF8_IGNORE);
            $to_return .= $decoded['message']['content'] ?? "";
        }

        return $to_return;
    }
}
