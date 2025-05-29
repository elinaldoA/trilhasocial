<x-app-layout>
    <h1 class="text-2xl font-semibold mb-6">
        Você {{ isset($seguidores) ? 'tem' : 'está seguindo' }}
        {{ isset($seguidores) ? 'seguidores' : '' }}
    </h1>

    @php
        $lista = $seguidores ?? $seguindo;
    @endphp

    @if($lista->isEmpty())
        <p class="text-gray-500">
            {{ isset($seguidores) ? 'Esse usuário ainda não tem seguidores.' : 'Esse usuário não está seguindo ninguém.' }}
        </p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($lista as $pessoa)
                <li class="flex items-center justify-between py-3">
                    <a href="{{ route('perfil.publico', $pessoa->username) }}" class="flex items-center space-x-4 hover:underline">
                        <!-- Foto de perfil pequena -->
                        @if ($pessoa->profile_photo_path)
                                    <img src="{{ asset('storage/' . $pessoa->profile_photo_path) }}" alt="Avatar"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm"
                            loading="lazy" />
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm"
                            loading="lazy" />
                        @endif
                        <span class="font-medium text-gray-900">{{ $pessoa->name }}</span>
                    </a>

                    @if(auth()->user()->id !== $pessoa->id)
                        <form method="POST" action="{{ route('follow.toggle', $pessoa) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-1 rounded-full text-sm font-semibold
                                    {{ auth()->user()->isFollowing($pessoa) ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-blue-600 text-white hover:bg-blue-700' }}
                                    transition"
                            >
                                {{ auth()->user()->isFollowing($pessoa) ? 'Deixar de seguir' : 'Seguir' }}
                            </button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
