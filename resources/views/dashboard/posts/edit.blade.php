<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Post: ') . $post->title }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('dashboard.posts.update', $post) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <x-input-label for="featured_image" :value="__('Featured Image')" />
                            <x-text-input id="featured_image" class="block mt-1 w-full" type="file" name="featured_image" accept="image/*" onchange="previewImage(this);" />
                            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />

                            @if ( $post->featured_image )
                                <div class="mt-2 max-w-32">
                                    <img src="{{ asset( $post->featured_image) }}" id="preview" alt="{{ $post->title }}" class="rounded-lg shadow-sm">
                                </div>
                            @endif
                        </div>

                        <script>
                            function previewImage(input) {
                                const preview = document.getElementById('preview');
                                if (input.files && input.files[0]) {
                                    const reader = new FileReader();
                                    
                                    reader.onload = function(e) {
                                        preview.src = e.target.result;
                                        preview.style.display = 'block';
                                    }
                                    
                                    reader.readAsDataURL(input.files[0]);
                                } else {
                                    preview.src = '';
                                    preview.style.display = 'none';
                                }
                            }
                        </script>

                        <div class="mt-4">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" value="{{ $post->title }}" type="text" name="title" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-textarea-input id="content" rows="6" class="block mt-1 w-full" name="content" required>
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
        </script>
    @endsection
</x-app-layout>