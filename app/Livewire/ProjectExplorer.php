<?php

namespace App\Livewire;

use App\Classes\OllamaClient;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectExplorer extends Component
{
	use WithFileUploads;

	public string $title = "Class comment generator";
	public ?TemporaryUploadedFile $class_file = null;

	public string $commented_class = '';
	protected OllamaClient $client;
	public string $contents = '';

	public function __construct()
	{
		$this->client = new OllamaClient();
	}

	public function analyze(): StreamedResponse
	{
		$this->commented_class = "Analyzing...";
		$this->validate([
			'class_file' => 'required'
		]);

		$this->contents = $this->class_file->getContent();
		$file_name = $this->class_file->getClientOriginalName();
		list($this->commented_class, $file_content) = $this->client->getCommentedClassContents($this->contents);

		return response()->streamDownload(function () use ($file_content) {
			echo $file_content;
		}, $file_name . '.php');
	}

	public function render()
	{
		return view('livewire.project-explorer');
	}
}
