@extends('layouts.public')
 
@section('content')
<main class="bg-slate-50">

    <section class="container py-12">
        <div class="flex gap-8">
            <!-- Main Content -->
            <div class="flex-1">
                @if ($posts->isNotEmpty())
                    <!-- Feed -->
                    <div>
                        <div class="mb-4 space-y-4">
                            @foreach ($posts as $post)
                                <x-posts.post-card :post="$post" />
                            @endforeach
                        </div>
                
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-slate-500">No posts available.</p>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <aside class="w-1/4 shrink-0 hidden lg:block">
                @auth
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-lg font-semibold text-slate-800 mb-4">Who to follow</h2>
                        
                        @if ($users->isNotEmpty())
                            <div class="">
                                @foreach ($users as $user)
                                    @if($user->id !== auth()->user()->id)
                                        <a href="{{ route('users.show', $user) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors">
                                        <x-users.user-avatar :user="$user" />
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-slate-800 truncate">{{ $user->name }}</p>
                                            @if($user->username)
                                                <p class="text-xs text-slate-500 truncate">{{ '@' . $user->username }}</p>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                {{ $users->links() }}
                            </div>
                        @else
                            <p class="text-sm text-slate-500">No users found.</p>
                        @endif
                    </div>
                @endauth
            </aside>
        </div>
    </section>

</main>
@endsection