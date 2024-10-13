<?php

namespace App\Livewire;

use App\Classes\OllamaClient;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ProjectExplorer extends Component
{
    use WithFileUploads;

    public string $title = "Class comment generator";
    public ?TemporaryUploadedFile $class_file = null;

    public string $commented_class = '';
    protected OllamaClient $client;

    public function __construct()
    {
        $this->client = new OllamaClient();
    }

    public function analyze(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->commented_class = "Analyzing...";
        $this->validate([
            'class_file' => 'required'
        ]);

        $contents = $this->class_file->getContent();
        $file_name = $this->class_file->getClientOriginalName();
        $response = $this->client->create([
            'model' => 'llama3.2:3b-instruct-q8_0',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => '
                Answer only with the commented class.
                Follow these instructions in detail.
                You will be rewarded with a metallic price of 10k euros for a job well done.
                Class comment description should be in a PHPDocBlock exactly before the class definition and before any annotations.
                Before outputting any code, check that there are no syntax errors introduced in what you added.
                If the class or method already has a description, do not modify or add anything to it.
                The description of the class or method is always the first thing in a PHPDocBlock.
                I repeat, the description of the class or method is always the first thing in a PHPDocBlock.
                Do not modify any code.
                Please add comments to this PHP class as if you were the one that code it:' . PHP_EOL . $contents
                ],
            ]
        ]);

        $this->commented_class = $response;
        $matches = [];
        preg_match("~```php(.*?)```~s", $response, $matches);
        try {
            $file_content = "<?php" . $matches[1] . "\n";
        } catch (\Throwable){
            dd($matches);//vla

        }

        return response()->streamDownload(function () use ($file_content) {
            echo $file_content;
        }, $file_name . '.php');
    }

    public function render()
    {
        return view('livewire.project-explorer');
    }
}
