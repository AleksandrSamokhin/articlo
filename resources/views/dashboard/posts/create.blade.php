<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('New Post') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="container">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('dashboard.posts.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label :value="__('Featured Image')" />
                            <x-text-input id="image" class="mt-1 cursor-pointer" type="file" name="image" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        
                        <livewire:post-content-generator />
{{-- 
                        <input type="hidden" name="title" wire:model="title">
                        <input type="hidden" name="content" wire:model="content"> --}}

                        <div class="mt-4">
                            <x-input-label for="categories" :value="__('Categories:')" />

                            <x-select name="categories[]" id="categories" class="block mt-1" multiple>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                            <x-input-error :messages="$errors->get('categories.*')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-primary-button>Create</x-primary-button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            FilePond.registerPlugin(FilePondPluginImagePreview);

            const inputElement = document.querySelector('input[type="file"]');
            const pond = FilePond.create(inputElement);
            FilePond.setOptions({
                imagePreviewMaxHeight: 320,
                server: {
                    process: '/upload',
                    revert: '/upload',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            });


            // function generateText(type, provider) {
            //     fetch('/text-generation', {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //         },
            //         body: JSON.stringify({
            //             type,
            //             provider: provider,
            //         })
            //     })
            //         .then(response => response.json())
            //         .then(data => {
            //             if (type === 'header') {
            //                 document.getElementById('content').value = data.text;
            //             }
            //         });
            // }

        </script>
    @endsection
</x-app-layout>