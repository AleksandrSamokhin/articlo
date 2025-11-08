<header class="bg-white border-b border-slate-200">
    <nav x-data="{ open: false }">
        <!-- Primary Navigation Menu -->
        <div class="container">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <x-application-logo class="block h-9 w-auto fill-current text-slate-800" />
                    </a>
                </div>                

                <!-- Navigation Links -->
                <div class="flex ml-auto">
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Home') }}
                        </x-nav-link>
                    </div>
                    
                    <div class="flex items-center space-x-4 ms-8">
                        <div class="hidden sm:flex">
                            @auth
                                <a href="{{ route('dashboard.posts.index') }}" class="self-center text-sm font-medium p-2 rounded-md border border-slate-200 text-slate-600 hover:text-slate-800">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="self-center text-sm font-medium text-slate-600 hover:text-slate-800">Login</a>
                            @endauth
                        </div>
            
                        <livewire:search />
                    </div>
                </div>


                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('dashboard.posts.index')" :active="request()->routeIs('dashboard.posts.*')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>
        </div>
    </nav>
</header>
