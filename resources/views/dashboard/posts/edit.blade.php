<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Edit Post: ') . $post->title }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="container">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form method="POST" action="{{ route('dashboard.posts.update', $post) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <x-input-label :value="__('Featured Image')" />
                            <x-text-input id="image" class="mt-1 cursor-pointer" type="file" name="image" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            
                            @if ($post->getFirstMediaUrl('posts', 'thumb-128'))
                                <div class="mt-2 max-w-32">
                                    <img src="{{ $post->getFirstMediaUrl('posts', 'thumb-128') }}" id="preview" alt="{{ $post->title }}" class="rounded-lg shadow-sm">
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" value="{{ $post->title }}" type="text" name="title" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-textarea-input id="content" rows="10" class="block mt-1 w-full" name="content" required>
                                {{ $post->content }}
                            </x-textarea-input>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="categories" :value="__('Categories:')" />

                            <x-select name="categories[]" id="categories" class="block mt-1" multiple>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($post->categories->contains($category->id))>{{ $category->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                            <x-input-error :messages="$errors->get('categories.*')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
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
        </script>
    @endsection
</x-app-layout>