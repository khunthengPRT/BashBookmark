<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Bash Snippets</h1>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            + New Snippet
        </button>
    </div>

    {{-- Create / Edit Form --}}
    @if ($showForm)
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-gray-800">
                {{ $editingId ? 'Edit Snippet' : 'New Snippet' }}
            </h2>

            <form wire:submit="save" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input wire:model="title" type="text" placeholder="e.g. List open ports"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Snippet Body</label>
                    <textarea wire:model="body" rows="5" placeholder="#!/usr/bin/env bash&#10;..."
                              class="mt-1 block w-full rounded-md border-gray-300 font-mono shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description <span class="text-gray-400">(optional)</span></label>
                    <textarea wire:model="description" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tags <span class="text-gray-400">(comma-separated)</span></label>
                    <input wire:model="tagsInput" type="text" placeholder="networking, debug, docker"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                    <button type="button" wire:click="cancelForm"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Snippet List --}}
    @if ($snippets->isEmpty())
        <p class="py-12 text-center text-gray-500">No snippets yet. Create your first one!</p>
    @else
        <div class="space-y-4">
            @foreach ($snippets as $snippet)
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-base font-semibold text-gray-900">{{ $snippet->title }}</h3>

                            @if ($snippet->description)
                                <p class="mt-1 text-sm text-gray-500">{{ $snippet->description }}</p>
                            @endif

                            <pre class="mt-3 overflow-x-auto rounded-md bg-gray-950 p-4 text-xs text-green-400">{{ $snippet->body }}</pre>

                            @if (!empty($snippet->tags))
                                <div class="mt-3 flex flex-wrap gap-1">
                                    @foreach ($snippet->tags as $tag)
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <p class="mt-2 text-xs text-gray-400">{{ $snippet->created_at->diffForHumans() }}</p>
                        </div>

                        <div class="flex shrink-0 gap-2">
                            <button wire:click="openEdit({{ $snippet->id }})"
                                    class="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Edit
                            </button>
                            <button wire:click="delete({{ $snippet->id }})"
                                    wire:confirm="Delete this snippet?"
                                    class="rounded-md border border-red-200 px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-50">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $snippets->links() }}
        </div>
    @endif

</div>
