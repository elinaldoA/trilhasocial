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
                    <a href="{{ route('perfil.publico', $pessoa->username) }}" class="flex items-center space-x-4 hover:underline transition">
                        @if ($pessoa->profile_photo_path)
                                    <img src="{{ asset('storage/' . $pessoa->profile_photo_path) }}" alt="Avatar"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm"
                            loading="lazy" />
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar"
                                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm"
                            loading="lazy" />
                        @endif
                        <span class="font-medium text-gray-900 text-lg">{{ $pessoa->name }}</span>
                    </a>

                    @if(auth()->user()->id !== $pessoa->id)
                        @if($isSeguidores)
                            <!-- Botão Remover seguidor -->
                            <form method="POST" action="{{ route('follow.remover_seguidor', $pessoa) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="px-5 py-2 rounded-full text-sm font-semibold bg-red-600 text-white hover:bg-red-700 transition"
                                    title="Remover seguidor"
                                >
                                    Remover seguidor
                                </button>
                            </form>
                        @else
                            <!-- Botão Seguir / Deixar de seguir para pessoas que o usuário segue -->
                            @php
                                $isFollowing = auth()->user()->isFollowing($pessoa);
                            @endphp
                            <form method="POST" action="{{ route('follow.toggle', $pessoa) }}">
                                @csrf
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
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
