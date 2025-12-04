@extends('layouts.public')

@section('content')
	<main class="bg-slate-50">
		<section class="container py-12">
			<h1 class="font-semibold text-xl text-slate-800 leading-tight mb-4">
				{{ __('Users') }}
			</h1>
			@if($users->count() > 0)
				<div class="space-y-2">
					@foreach($users as $user)
						<a href="{{ route('users.show', $user->username) }}" class="p-4 bg-white rounded-lg shadow-sm flex items-center gap-4 hover:shadow-lg transition-shadow">
							<span class="text-slate-900 hover:text-slate-600">{{ $user->name }}</span>
						</a>
					@endforeach
				</div>
				<div class="mt-4">
					{{ $users->links() }}
				</div>
			@else
				<p class="text-slate-500">{{ __('No users found') }}</p>
			@endif
		</section>
	</main>
@endsection