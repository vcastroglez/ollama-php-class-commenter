<?php

namespace App\Livewire;

use App\Classes\OllamaClient;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ProjectExplorer extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $class_file = null;

    public array $commented_class = [];
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
                ['role' => 'system', 'content' => "You're a senior php developer specialized in analysing php Classes and adding comments to the class so that your other team members can understand it by reading your comments, you need to add comments to lines that may be confusing"],
                ['role' => 'system', 'content' => "Answer only with the class with the comments added"],
                ['role' => 'user', 'content' => 'Please add comments to this PHP class:' . PHP_EOL . $contents],
            ]
        ]);
        dd($response);//vla

        $this->commented_class = $response->toArray();
    }

    public function render()
    {
        return view('livewire.project-explorer');
    }
}
