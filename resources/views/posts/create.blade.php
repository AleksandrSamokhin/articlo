<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Post') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label for="featured_image" :value="__('Featured Image')" />
                            <x-text-input id="featured_image" class="block mt-1 w-full" type="file" name="featured_image" accept="image/*" onchange="previewImage(this);" />
                            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
                            <div class="mt-2 max-w-32">
                                <img id="preview" src="" alt="Preview" style="max-width: 200px; display: none;" class="mt-2 rounded-lg shadow-sm">
                            </div>
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
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-textarea-input id="content" rows="6" class="block mt-1 w-full" name="content" required />
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>
 

                        <div class="mt-4">
                            <div>
                                <label for="category_id">Category:</label>
                            </div>
                            <select name="category_id" id="category_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-primary-button>Create</x-primary-button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>