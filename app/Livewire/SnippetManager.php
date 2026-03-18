<?php

namespace App\Livewire;

use App\Models\Snippet;
use Livewire\Component;
use Livewire\WithPagination;

class SnippetManager extends Component
{
    use WithPagination;

    // Form state
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $body = '';
    public string $description = '';
    public string $tagsInput = '';  // comma-separated tag string

    protected array $rules = [
        'title' => 'required|string|max:255',
        'body'  => 'required|string',
        'description' => 'nullable|string|max:1000',
        'tagsInput'   => 'nullable|string',
    ];

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $snippet = Snippet::findOrFail($id);
        $this->editingId   = $snippet->id;
        $this->title       = $snippet->title;
        $this->body        = $snippet->body;
        $this->description = $snippet->description ?? '';
        $this->tagsInput   = implode(', ', $snippet->tags ?? []);
        $this->showForm    = true;
    }

    public function save(): void
    {
        $this->validate();

        $tags = array_values(
            array_filter(array_map('trim', explode(',', $this->tagsInput)))
        );

        $data = [
            'title'       => $this->title,
            'body'        => $this->body,
            'description' => $this->description ?: null,
            'tags'        => $tags ?: null,
        ];

        if ($this->editingId) {
            Snippet::findOrFail($this->editingId)->update($data);
        } else {
            Snippet::create($data);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Snippet::findOrFail($id)->delete();
        $this->resetPage();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId   = null;
        $this->title       = '';
        $this->body        = '';
        $this->description = '';
        $this->tagsInput   = '';
        $this->resetValidation();
    }

    public function render()
    {
        $snippets = Snippet::latest('created_at')->paginate(10);

        return view('livewire.snippet-manager', compact('snippets'));
    }
}
