<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('New Post') }}
        </h2>
    </x-slot>
 
    <div class="py-12" x-data="{}" x-on:category-created.window="window.location.reload()">
        <div class="container">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-slate-800 dark:text-red-400" role="alert">
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

                        <div class="mt-4">
                            <x-input-label for="categories" :value="__('Categories:')" />

                            <x-select name="categories[]" id="categories" class="block mt-1" multiple>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-select>

                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                            <x-input-error :messages="$errors->get('categories.*')" class="mt-2" />

                            <button type="button"
                            onclick="Livewire.dispatch('openModal', {'component': 'post-create-category'})"
                            class="mt-2 text-sm text-blue-600 hover:text-blue-500">Create a new category</button>
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