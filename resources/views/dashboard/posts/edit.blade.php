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
                            <x-input-label for="featured_image" :value="__('Featured Image')" />
                            <x-text-input id="featured_image" class="mt-1 cursor-pointer" type="file" name="featured_image" accept="image/*" />
                            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />

                            <div class="mt-2 max-w-32">
                                <img src="{{ $post->getFirstMediaUrl('posts', 'thumb-128') ?: '' }}" id="preview" alt="{{ $post->title }}" class="rounded-lg shadow-sm" style="{{ $post->getFirstMediaUrl('posts', 'thumb-128') ? '' : 'display: none;' }}">
                            </div>
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
                            <x-input-label for="category_id" :value="__('Category:')" />

                            <x-select name="category_id" id="category_id" class="block mt-1">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($category->id == $post->category_id)>{{ $category->name }}</option>
                                @endforeach
                            </x-select>
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
            const inputElement = document.querySelector('input[id="featured_image"]');
            const pond = FilePond.create(inputElement);
            FilePond.setOptions({
                server: {
                    url: '/upload',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            });

            // Show preview after file is added (immediate preview)
            pond.on('addfile', (error, file) => {
                if (error) {
                    console.error('Error adding file:', error);
                    return;
                }
                
                const preview = document.getElementById('preview');
                if (preview && file.file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file.file);
                }
            });

            // Update preview after successful upload (XHR response)
            pond.on('processfile', (error, file) => {
                if (error) {
                    console.error('Error processing file:', error);
                    return;
                }
                
                const preview = document.getElementById('preview');
                if (preview && file.file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file.file);
                }
            });

            // Hide preview when file is removed
            pond.on('removefile', () => {
                const preview = document.getElementById('preview');
                if (preview) {
                    preview.src = '';
                    preview.style.display = 'none';
                }
            });
        </script>
    @endsection
</x-app-layout>