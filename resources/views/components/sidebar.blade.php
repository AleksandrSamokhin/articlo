<aside class="lg:w-1/4 w-full bg-white p-6 shadow-md rounded-lg">
		<h2 class="text-xl font-semibold mb-4">Categories</h2>
		<ul class="space-y-2">
				@foreach ($categories as $category)
						<li><a href={{ route('categories.show', $category) }} class="text-slate-600 hover:text-slate-800">{{ $category->name }}</a></li>							
				@endforeach
		</ul>
</aside>