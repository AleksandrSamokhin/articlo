@extends('layouts.public')
 
@section('content')
<main class="bg-slate-50 flex gap-6">

    <section class="container py-12">
        <div class="flex flex-wrap gap-8">
            <div class="flex-1">
                @if ($posts->isNotEmpty())
                    <div>
                        <h1 class="text-xl font-semibold mb-4">Posts in {{ $category->name }}</h1>
                        <div class="space-y-6 mb-4">
                            @forelse ($posts as $post)
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
            @include('layouts.sidebar-categories')
        </div>        
    </section>

</main>
@endsection