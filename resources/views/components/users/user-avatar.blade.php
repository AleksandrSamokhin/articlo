@props(['user', 'class' => 'w-10 h-10'])

@if ($user->getFirstMediaUrl('avatars', 'thumb-64'))
	<div class="{{ $class }} rounded-full overflow-hidden shrink-0">
		<img class="w-full h-full object-cover" src="{{ $user->getFirstMediaUrl('avatars', 'thumb-64') }}" alt="{{ $user->name }}">
	</div>
@else
	<div class="{{ $class }} rounded-full overflow-hidden bg-slate-200 shrink-0 flex items-center justify-center">
		<span class="text-slate-500 uppercase">{{ $user->name[0] }}</span>
	</div>
@endif