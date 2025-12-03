@extends('layouts.public')

@section('content')
    <main class="bg-slate-50">
        <section class="container py-12">
            <div class="mb-8">
                <h1 class="font-semibold text-xl text-slate-800 leading-tight mb-4">
                    {{ __('Profile:') }} {{ $user->name }}
                </h1>

                @if($posts->count() > 0)
                    <div class="w-full min-w-full space-y-2">
                        @foreach($posts as $post)
                            <a href="{{ route('posts.show', $post->slug) }}" class="p-4 bg-white rounded-lg shadow-sm flex items-center gap-4 hover:shadow-lg transition-shadow">
                                <div>
                                    @if($post->getFirstMediaUrl('posts', 'thumb-128'))
                                        <img src="{{ $post->getFirstMediaUrl('posts', 'thumb-128') }}" alt="{{ $post->title }}" width="40" height="40" class="h-10 w-10 rounded-full object-cover">
                                    @endif
                                </div>            
                                    
                                <div class="whitespace-nowrap text-sm text-slate-900 flex space-x-2">
                                    <span href="{{ route('posts.show', $post->slug) }}" class="text-slate-900 hover:text-slate-600">{{ $post->title }}</span>
                                    <div class="text-sm text-slate-500">
                                        {{ $post->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>                                    
                        @endforeach
                    </div>
                    @else
                        <p class="text-slate-500">No posts found</p>
                    @endif
                </div>
            </section>
        </main>
    @endsection
