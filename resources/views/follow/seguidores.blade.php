<x-app-layout>
    @php
        $isSeguidores = isset($seguidores);
        $lista = $seguidores ?? $seguindo;
    @endphp

    <h1 class="text-3xl font-bold mb-8 text-gray-800">
        Você {{ $isSeguidores ? 'tem' : 'está seguindo' }}
        {{ $isSeguidores ? $lista->count() . ' seguidor' . ($lista->count() !== 1 ? 'es' : '') : '' }}
    </h1>

    @if($lista->isEmpty())
        <p class="text-gray-500 italic">
            {{ $isSeguidores
                ? 'Esse usuário ainda não tem seguidores.'
                : 'Esse usuário não está seguindo ninguém.'
            }}
        </p>
    @else
        <ul class="divide-y divide-gray-300">
            @foreach($lista as $pessoa)
                <li class="flex items-center justify-between py-4">
                    <a href="{{ route('profile.edit', $pessoa->id) }}" class="flex items-center space-x-4 hover:underline transition">
                        @php
                            $photoUrl = $pessoa->profile_photo_path
                                ? asset('storage/' . $pessoa->profile_photo_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($pessoa->name) . '&background=random';
                        @endphp
                        <img
                            src="{{ $photoUrl }}"
                            alt="{{ $pessoa->name }}"
                            class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm"
                            loading="lazy"
                        >
                        <span class="font-medium text-gray-900 text-lg">{{ $pessoa->name }}</span>
                    </a>

                    @if(auth()->user()->id !== $pessoa->id)
                        <form method="POST" action="{{ route('follow.toggle', $pessoa) }}">
                            @csrf
                            @php
                                $isFollowing = auth()->user()->isFollowing($pessoa);
                            @endphp
                            <button
                                type="submit"
                                class="px-5 py-2 rounded-full text-sm font-semibold transition
                                    {{ $isFollowing
                                        ? 'bg-red-600 text-white hover:bg-red-700'
                                        : 'bg-blue-600 text-white hover:bg-blue-700'
                                    }}"
                            >
                                {{ $isFollowing ? 'Deixar de seguir' : 'Seguir' }}
                            </button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
