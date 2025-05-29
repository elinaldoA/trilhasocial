<x-app-layout>
    <div class="max-w-3xl mx-auto mt-8 p-4 text-center">
        @if ($user->profile_photo_path)
            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Avatar"
                class="mx-auto w-24 h-24 rounded-full mb-4">
        @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar"
                class="mx-auto w-24 h-24 rounded-full mb-4">
        @endif

        <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
        <p class="text-sm text-gray-600">@ {{ $user->username }}</p>

        <p class="mt-4 text-gray-700 dark:text-gray-300">
            Este perfil é privado.<br>Siga este usuário para ver as publicações.
        </p>

        <div class="mt-6">
            @guest
                <a href="{{ route('login') }}" class="text-blue-500 hover:underline">
                    Entrar para seguir
                </a>
            @else
                @if ($hasPendingRequest)
                    <button disabled class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                        Aguardando aprovação
                    </button>
                @else
                    <form action="{{ route('follow.toggle', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Seguir
                        </button>
                    </form>
                @endif
            @endguest
        </div>
    </div>
</x-app-layout>
