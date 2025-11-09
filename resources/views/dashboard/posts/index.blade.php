<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="container">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-slate-200">

					@if (session('status') || session('success'))
						<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-slate-800 dark:text-green-400" role="alert">
							{{ session('status') ?? session('success') }}
						</div>
					@endif

					<x-primary-button tag="a" href="{{ route('dashboard.posts.create') }}">Add new post</x-primary-button>

                    <br /><br />
					<table class="w-full min-w-full divide-y divide-slate-200">
						<thead class="bg-slate-50">
							<tr>
								<th scope="col" class="w-16 px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
									Image
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
									Title
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
									Categories
								</th>
								<th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
									Actions
								</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-slate-200">
							@foreach($posts as $post)
								<tr>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
										@if($post->getFirstMediaUrl('posts', 'thumb-128'))
											<img src="{{ $post->getFirstMediaUrl('posts', 'thumb-128') }}" alt="{{ $post->title }}" width="64" height="64" class="h-10 w-10 rounded-full object-cover">
										@endif
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
										{{ $post->title }}
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
										@foreach($post->categories as $category)
											<span class="text-sm text-slate-500">
												{{ $category->name }}@if (!$loop->last),@endif
											</span>
										@endforeach
									</td>
									
									<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
										@can('update', $post)
											<a href="{{ route('dashboard.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
										@endcan

										@can('delete', $post)
											<form method="POST" action="{{ route('dashboard.posts.destroy', $post) }}" class="inline-block">
												@csrf
												@method('DELETE')
												<button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
											</form>
										@endcan
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>

					{{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>