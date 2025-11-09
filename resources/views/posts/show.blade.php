@extends('layouts.blog')
 
@section('content')
    <main class="container">
        <section class="py-12">
            <div class="mb-8">
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
                
                <div class="flex justify-center items-center gap-4 text-sm text-slate-400">
                    <span class="flex items-center gap-1 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                    <p>By {{ $post->user->name }}</p>
                </div>
            </div>            
            
            {{-- Display the post image --}}
			@if ( $post->getFirstMediaUrl('posts', 'thumb-1170') )
                <img src="{{ asset( $post->getFirstMediaUrl('posts', 'thumb-1170') ) }}" alt="{{ $post->title }}"/>
			@endif
            <div class="prose prose-lg mx-auto">
                {!! $post->content !!}
            </div>
            
            <livewire:post-comments :post="$post" />

        </section>        
    </main>
@endsection