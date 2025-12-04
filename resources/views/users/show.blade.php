@extends('layouts.public')

@section('content')
    <main class="bg-slate-50">
        <section class="container py-12">
            <div class="mb-8">

                @if (session('error') || session('success'))
                    <div class="p-4 mb-4 text-sm {{ session('error') ? 'text-red-800' : 'text-green-800' }} rounded-lg {{ session('error') ? 'bg-red-50' : 'bg-green-50' }} dark:bg-slate-800 dark:text-{{ session('error') ? 'red-400' : 'green-400' }}" role="alert">
                        {{ session('error') ?? session('success') }}
                    </div>
                @endif

                <div class="flex items-center space-x-3 mb-4">
                    <x-users.user-avatar :user="$user" />
                    <h1 class="font-semibold text-xl text-slate-800 leading-tight">
                        {{ $user->name }}
                    </h1>
                    
                    @auth
                        @if($user->id !== auth()->user()->id && !auth()->user()->isFollowing($user))
                            <form action="{{ route('users.follow', $user->id) }}" method="POST">
                            @csrf
                                @method('POST')
                                <x-primary-button size="sm" type="submit">Follow</x-primary-button>
                            </form>
                        @elseif($user->id !== auth()->user()->id && auth()->user()->isFollowing($user))
                            <form action="{{ route('users.unfollow', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-primary-button size="sm" type="submit">Unfollow</x-primary-button>
                            </form>
                        @endif
                    @endauth
                </div>

                @if($posts->isNotEmpty())
                    <h2 class="text-lg font-medium text-slate-900 mb-4">Posts</h2>
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
