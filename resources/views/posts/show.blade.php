@extends('layouts.blog')
 
@section('content')
    <main class="container mx-auto mt-6 flex justify-center">
        <section class="w-3/5 bg-white p-6 shadow-md rounded-lg">
            <h1 class="text-2xl font-bold mb-4">{{ $post->title }}</h1>
            <p class="text-sm text-gray-500 mb-4">Published on {{ $post->created_at->format('F j, Y') }}</p>
            <p class="text-sm text-gray-500 mb-4">By {{ $post->user->name }}</p>
            <p class="text-sm text-gray-500 mb-4">Category: {{ $post->category->name }}</p>
            
            <livewire:post-comment-count :count="$post->comments->count()" />
            
            {{-- Display the post image --}}
			@if ( $post->getFirstMediaUrl('posts', 'thumb-1170') )
                <img src="{{ asset( $post->getFirstMediaUrl('posts', 'thumb-1170') ) }}" alt="{{ $post->title }}"/>
                {{-- <img src="{{ asset( Storage::disk('s3')->url($post->featured_image) ) }}" alt="{{ $post->title }}"/> --}}
			@endif
            <p class="mt-4 text-lg text-gray-600">{!! $post->content !!}</p>
            
            <livewire:post-comments :post="$post" />

        </section>        
    </main>
@endsection