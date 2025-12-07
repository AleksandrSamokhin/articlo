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
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('Users') }}
                    </x-nav-link>
                </div>

                <div class="flex ml-auto">
                    <div class="flex items-center space-x-4 ms-8">
                        <div class="hidden sm:flex">
                            @auth
                                <a href="{{ route('dashboard.posts.index', absolute: false) }}" class="self-center text-sm font-medium text-slate-600 hover:text-slate-800 mr-4">{{ auth()->user()->name }}</a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-secondary-button tag="button" size="sm" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-secondary-button>
                                </form>
                            @else
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('login') }}" class="self-center text-sm font-medium text-slate-600 hover:text-slate-800">Login</a>
                                    <x-primary-button tag="a" size="sm" href="{{ route('register') }}">Sign Up</x-primary-button>
                                </div>
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
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                @auth
                    <x-responsive-nav-link :href="route('dashboard.posts.index')" :active="request()->routeIs('dashboard.posts.*')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @endauth
                @guest
                    <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endguest
            </div>
        </div>
    </nav>
</header>
