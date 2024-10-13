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
		$response = $client->post($this->host . '/api/chat', [
			'body' => json_encode($params)
		]);

		$content = $response->getBody()->getContents();
		$exploded = explode(PHP_EOL, $content);
		$to_return = "";
		foreach ($exploded as $item) {
			$decoded = json_decode($item, true, flags: JSON_INVALID_UTF8_IGNORE);
			$to_return .= $decoded['message']['content'] ?? "";
		}

		return $to_return;
	}

	/**
	 * @param string $contents
	 *
	 * @return array
	 */
	public function getCommentedClassContents(string $contents): array
	{
		$response = $this->create([
			'model' => 'llama3.2:3b-instruct-q8_0',
			'messages' => [
				[
					'role' => 'user',
					'content' => '
                You will be rewarded with a metallic price of 10k euros for a job well done.
                Answer only with the whole class.
                Answer only what is asked from you and nothing else, I am not asking for any analysis or insight.
                Class comment description should be in a PHPDocBlock exactly before the class definition and before any annotations.
                Before outputting any code, check that there are no syntax errors introduced in what you added.
                Do not modify the code.
                Follow these instructions in detail.
                Please add comments to this PHP class as if you were the one that code it:' . PHP_EOL . $contents
				],
			]
		]);

		$matches = [];
		preg_match("~```php(.*?)```~s", $response, $matches);
		try {
			$file_content = "<?php" . $matches[1] . "\n";
		} catch (\Throwable) {
			dd($matches);//vla
		}

		return [$response, $file_content];
	}
}
