<x-app-layout>
    <h1 class="text-3xl font-bold mb-8 text-gray-800">
        Solicitações de Seguidores ({{ $solicitacoes->count() }})
    </h1>

    @if($solicitacoes->isEmpty())
        <p class="text-gray-500 italic">Você não tem solicitações pendentes.</p>
    @else
        <ul class="divide-y divide-gray-300">
            @foreach($solicitacoes as $pessoa)
                <li class="flex items-center justify-between py-4">
                    <a href="{{ route('perfil.publico', $pessoa->username) }}" class="flex items-center space-x-4 hover:underline transition">
                        <img
                            src="{{ asset('storage/' .$pessoa->profile_photo_path) }}"
                            alt="{{ $pessoa->name }}"
                            class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm"
                            loading="lazy"
                        >
                        <span class="font-medium text-gray-900 text-lg">{{ $pessoa->name }}</span>
                    </a>

                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('follow.aceitar', $pessoa) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-full text-sm hover:bg-green-700 transition"
                            >
                                Aceitar
                            </button>
                        </form>

                        <form method="POST" action="{{ route('follow.rejeitar', $pessoa) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-full text-sm hover:bg-red-700 transition"
                            >
                                Rejeitar
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
