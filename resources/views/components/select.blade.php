<select {{ $attributes->merge(['class' => 'block mt-1 rounded-md border-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 ']) }}>
		{{ $slot }}
</select>
