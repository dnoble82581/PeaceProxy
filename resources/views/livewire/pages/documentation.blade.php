<?php

	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Title;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Str;

	new #[Layout ('layouts.guest')] #[Title('Documentation')] class extends \Livewire\Volt\Component {
		public $documentationHtml;

		public function mount()
		{
			// Get the markdown content from the overview.md file
			$markdownPath = base_path('docs/user-guide/overview.md');
			$markdown = File::exists($markdownPath)? File::get($markdownPath) : 'Documentation not found.';

			// Parse the markdown to HTML
			$html = Str::markdown($markdown);

			// Replace relative image paths with absolute paths
			$html = str_replace('src="../../assets/', 'src="'.asset('assets/').'/', $html);

			$this->documentationHtml = $html;
		}
	}

?>

<div class="container mx-auto px-4 py-8">
	<div class="prose max-w-none">
		{!! $documentationHtml !!}
	</div>
</div>
