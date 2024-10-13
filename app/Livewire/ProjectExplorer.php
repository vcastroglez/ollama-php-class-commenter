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

    public function analyze(): void
    {
        $this->validate([
            'class_file' => 'required'
        ]);

        $contents = $this->class_file->getContent();
        $response = $this->client->create([
            'model' => 'llama3.2:3b-instruct-q8_0',
            'messages' => [
                ['role' => 'user', 'content' => 'Answer only with the commented class. Please add comments to this PHP class:' . PHP_EOL . $contents],
            ]
        ]);

        $this->commented_class = $response;
    }

    public function render()
    {
        return view('livewire.project-explorer');
    }
}
