<nav x-data="{ open: false }"
    class="bg-white dark:bg-black border-b border-gray-200 dark:border-gray-800 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <!-- Logo -->
        <a href="{{ route('feed') }}" class="flex items-center space-x-2">
            <x-application-logo class="h-8 w-auto text-black dark:text-white" />
            <span class="font-semibold text-lg text-gray-800 dark:text-white hidden sm:block">TrilhaSocial</span>
        </a>

        <!-- Search (Desktop only) -->
        <div class="hidden sm:block flex-1 max-w-md mx-4">
            <form action="{{ route('buscar') }}" method="GET">
                <input type="text" name="buscar" placeholder="Buscar pessoas ou trilhas"
                    class="w-full px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-800 text-sm text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-300 dark:border-gray-700" />
            </form>
        </div>

        <!-- Desktop Ações -->
        <div class="hidden sm:flex items-center space-x-5 text-gray-600 dark:text-gray-300">
            <!-- Botões e ícones -->
            <a href="{{ route('trails.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-full text-sm font-medium transition duration-200">+</a>

            <!-- Ícones -->
            <a href="{{ route('feed') }}" title="Feed">
                <svg class="h-6 w-6 hover:text-blue-600 dark:hover:text-white" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l9-9 9 9v8a2 2 0 01-2 2h-4v-6H9v6H5a2 2 0 01-2-2v-8z" />
                </svg>
            </a>

            @php
                $unreadMessagesCount = \App\Models\Message::where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            @endphp

            <a href="{{ route('messages.index') }}" class="relative" title="Mensagens">
                <svg class="h-6 w-6 hover:text-blue-600 dark:hover:text-white" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h12a2 2 0 012 2z" />
                </svg>
                @if($unreadMessagesCount > 0)
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                        {{ $unreadMessagesCount }}
                    </span>
                @endif
            </a>

            @php
                $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
            @endphp

            <a href="{{ route('notifications') }}" class="relative" title="Notificações">
                <svg class="h-6 w-6 hover:text-blue-600 dark:hover:text-white" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.4-1.4A2 2 0 0118 14v-3a6 6 0 00-12 0v3a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1" />
                </svg>
                @if($unreadNotificationsCount > 0)
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                        {{ $unreadNotificationsCount }}
                    </span>
                @endif
            </a>


            <!-- Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center space-x-2 focus:outline-none hover:bg-gray-100 dark:hover:bg-gray-800 px-2 py-1 rounded-full">
                        <img class="h-8 w-8 rounded-full object-cover"
                            src="{{ asset('storage/' . Auth::user()->profile_photo_path ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name)) }}"
                            alt="Avatar">
                        <span class="hidden lg:block text-sm font-medium text-gray-800 dark:text-white">{{ Auth::user()->name }}</span>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                    <x-dropdown-link :href="route('follow.seguidores', auth()->user())">Meus Seguidores</x-dropdown-link>
                    <x-dropdown-link :href="route('follow.seguindo', auth()->user())">Seguindo</x-dropdown-link>
                    <x-dropdown-link :href="route('follow.sugestoes')">Sugestões</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Sair</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <!-- Mobile Toggle -->
        <div class="sm:hidden">
            <button @click="open = !open"
                class="p-2 text-gray-600 dark:text-gray-300 focus:outline-none hover:text-blue-600 dark:hover:text-white">
                <!-- Hamburger -->
                <svg :class="{ 'hidden': open, 'inline-flex': !open }" class="h-6 w-6" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <!-- Close -->
                <svg :class="{ 'hidden': !open, 'inline-flex': open }" class="h-6 w-6 hidden" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{ 'block': open, 'hidden': !open }"
        class="sm:hidden hidden bg-white dark:bg-black border-t border-gray-100 dark:border-gray-700 transition-all duration-300">
        <div class="pt-4 pb-4 px-4 space-y-3">
            <!-- Perfil -->
            <div class="flex items-center space-x-3">
                <img class="h-9 w-9 rounded-full object-cover"
                    src="{{ asset('storage/' .Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name)) }}"
                    alt="Avatar">
                <div>
                    <div class="text-gray-800 dark:text-white font-medium">{{ Auth::user()->name }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <!-- Navegação -->
            <x-responsive-nav-link :href="route('trails.index')">Trilhas</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('messages.index')">Mensagens</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('notifications')">Notificações</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('follow.seguidores', auth()->user())">Seguidores</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('follow.seguindo', auth()->user())">Seguindo</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('follow.sugestoes')">Sugestões</x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Sair</x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
