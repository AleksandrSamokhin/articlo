@extends('layouts.blog')
 
@section('content')
    <main class="container">
        <section class="py-12">
            <div class="mb-6">
                @if($post->categories->isNotEmpty())
                    <div class="flex gap-2 flex-wrap justify-center mb-4">
                        @foreach($post->categories as $category)
                            <a href="{{ route('categories.show', $category->slug) }}" 
                            class="text-sm hover:border-blue-600 px-4 py-1 rounded-md bg-blue-50 border border-blue-50 text-blue-600">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <h1 class="text-center">{{ $post->title }}</h1>
                
                <div class="flex justify-center">
                    <p class="text-sm text-gray-500 mb-4">Published on {{ $post->created_at->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-500 mb-4">By {{ $post->user->name }}</p>
                    <livewire:post-comment-count :count="$post->comments->count()" />
                </div>
            </div>
            
            
            {{-- Display the post image --}}
			@if ( $post->getFirstMediaUrl('posts', 'thumb-1170') )
                <img src="{{ asset( $post->getFirstMediaUrl('posts', 'thumb-1170') ) }}" alt="{{ $post->title }}"/>
                {{-- <img src="{{ asset( Storage::disk('s3')->url($post->featured_image) ) }}" alt="{{ $post->title }}"/> --}}
			@endif
            <div class="prose prose-lg mx-auto">
                {!! $post->content !!}
            </div>
            
            <livewire:post-comments :post="$post" />

        </section>        
    </main>
@endsection