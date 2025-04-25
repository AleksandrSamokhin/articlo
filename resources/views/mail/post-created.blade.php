<h2>
	{{ $post->title }}
</h2>

<p>Hi {{ $user->name }},</p>
<p>Congrats! Your post has been posted.</p>

<p>
	<a href="{{ url('/posts/' . $post->slug) }}">View Your Post</a>
</p>