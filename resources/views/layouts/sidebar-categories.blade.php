<aside class="md:w-1/4 w-full">
	<div class="bg-white rounded-lg shadow-md p-6">
		<h2 class="text-xl font-semibold mb-4">Categories</h2>
		<ul class="space-y-2">
			@foreach ($categories as $category)
				@php
					$isActive = request()->routeIs('categories.show') && request()->route('category')?->id === $category->id;
				@endphp
				<li><a href={{ route('categories.show', $category) }} class="text-slate-600 hover:text-slate-800 {{ $isActive ? 'text-slate-800 font-semibold' : '' }}">{{ $category->name }}</a></li>							
			@endforeach
		</ul>
	</div>
</aside>