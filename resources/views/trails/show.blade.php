<x-app-layout>
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $trail->name }}</h1>

        {{-- Botão de seguir --}}
        @auth
            @if(auth()->id() !== $trail->user->id)
                <form method="POST" action="{{ route('follow.toggle', $trail->user->id) }}" class="mb-4">
                    @csrf
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        {{ auth()->user()->isFollowing($trail->user) ? 'Deixar de seguir' : 'Seguir ' . $trail->user->name }}
                    </button>
                </form>
            @endif
        @endauth

        <p class="text-gray-700 mb-3"><strong>Descrição:</strong> {{ $trail->description }}</p>
        <p class="text-gray-700 mb-3"><strong>Localização:</strong> {{ $trail->location }}</p>
        <p class="text-gray-700 mb-3"><strong>Dificuldade:</strong> {{ $trail->difficulty }}</p>
        <p class="text-gray-700 mb-3"><strong>Distância:</strong> {{ $trail->distance }} km</p>
        <p class="text-gray-700 mb-6"><strong>Tempo médio:</strong> {{ $trail->avg_time }} minutos</p>

        @if ($trail->images->count())
            <div class="mb-6">
                <p class="text-gray-700 font-medium mb-3">Imagens da Trilha:</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($trail->images as $image)
                        <div class="rounded overflow-hidden shadow">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem da Trilha"
                                class="w-full h-32 object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex gap-4">
            <a href="{{ route('trails.edit', $trail) }}"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-semibold transition">
                Editar
            </a>

            <form action="{{ route('trails.destroy', $trail) }}" method="POST"
                onsubmit="return confirm('Tem certeza que deseja excluir?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-block bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md font-semibold transition">
                    Excluir
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
