<li>
    <a href="{{ route('posts.show', $post) }}" class="flex-1 min-w-0 group">
        <h3 class="text-sm font-semibold text-slate-600 line-clamp-2 hover:underline group-hover:text-slate-900 transition-colors">
            {{ $post->title }}
        </h3>
    </a>
</li>