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
                Class comment description should be before the class definition
                Please add comments to this PHP class:' . PHP_EOL . $contents
                ],
            ]
        ]);

        $matches = [];
        preg_match("~```php(.*?)```~s", $response, $matches);
        $this->commented_class = "<?php".$matches[1]."\n";

        return response()->streamDownload(function () {
            echo $this->commented_class;
        }, $file_name . '.php');
    }

    public function render()
    {
        return view('livewire.project-explorer');
    }
}
