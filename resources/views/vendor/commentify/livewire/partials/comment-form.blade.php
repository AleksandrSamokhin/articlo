@if (!config('commentify.read_only'))
    <form class="mb-6" wire:submit="{{$method}}">
        @if (session()->has('message'))
            @php
                $alertType = session('alertType', 'success');
                $alertClasses = [
                    'success' => 'text-green-800 bg-green-50 dark:text-green-400',
                    'warning' => 'text-yellow-800 bg-yellow-50 dark:text-yellow-400',
                    'error'   => 'text-red-800 bg-red-50 dark:text-red-400',
                ];
            @endphp
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <div class="p-4 mb-4 text-sm rounded-lg {{ $alertClasses[$alertType] ?? $alertClasses['success'] }}"
                     role="alert">
                    <span class="font-medium">{{ ucfirst($alertType) }}!</span> {{ session('message') }}
                </div>
            </div>
        @endif
        @csrf
        <div
            class="group py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-slate-200 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition-colors dark:bg-slate-800 dark:border-slate-700 dark:focus-within:border-blue-500             ">
            <label for="{{$inputId}}" class="sr-only">{{$inputLabel}}</label>
            <div x-data="{
        detectAtSymbol(event) {
            const textarea = event.target;
            const cursorPosition = textarea.selectionStart;
            const textBeforeCursor = textarea.value.substring(0, cursorPosition);
            const atSymbolPosition = textBeforeCursor.lastIndexOf('@');
            if (atSymbolPosition !== -1) {
                const searchTerm = textBeforeCursor.substring(atSymbolPosition + 1);
                if (searchTerm.trim().length > 0) {
                    this.$wire.getUsers(searchTerm);
                }
            }
        }
    }">
        <textarea id="{{$inputId}}" rows="6"
                  class="px-0 w-full text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none
                              dark:text-white dark:placeholder-slate-400 dark:bg-slate-800 @error($state.'.body')
                              border-red-500 @enderror"
                  placeholder="{{ __('commentify::commentify.comments.write_comment') }}"
                  wire:model.live="{{$state}}.body"
                  @input="detectAtSymbol"
        ></textarea>
            </div>
            @if(!empty($users) && $users->count() > 0)
                @include('commentify::livewire.partials.dropdowns.users')
            @endif
            @error($state.'.body')
            <p class="mt-2 text-sm text-red-600">
                {{$message}}
            </p>
            @enderror
        </div>


        <x-primary-button wire:loading.attr="disabled" type="submit">
            <span wire:loading wire:target="{{ $method }}" class="mr-2">
                @include('commentify::livewire.partials.loader')
            </span>
            <span wire:loading.remove wire:target="{{ $method }}">
                {{ $button }}
            </span>
        </x-primary-button>

    </form>
@else
    <div class="text-slate-500 italic">{{ __('commentify::commentify.comments.read_only_message') }}</div>
@endif
