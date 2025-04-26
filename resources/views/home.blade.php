@extends('layouts.blog')
 
@section('content')
<main class="container mx-auto mt-6 lg:px-8 px-6 flex lg:flex-nowrap flex-wrap gap-6">

    <!-- Posts -->
    <section class="lg:w-3/4 w-full">

        <!-- Featured -->
        @if ($featuredPosts->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Featured Posts</h2>
                <div class="grid place-items-start mb-4 md:gap-6 gap-8 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($featuredPosts as $post)
                        <x-posts.post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        @endif
        
        @if ($posts->isNotEmpty())
            <!-- Latest -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Latest Posts</h2>
                <div class="grid place-items-start mb-4 md:gap-6 gap-8 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-posts.post-card :post="$post" />
                    @endforeach
                </div>
        
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center">
                <p class="text-gray-500">No posts available.</p>
            </div>
        @endif

    </section>

    <x-sidebar :categories="$categories" />

</main>
@endsection