<x-app-layout>
    <h1 class="text-xl font-bold mb-4">Sugestões para você</h1>

    @forelse($sugestoes as $pessoa)
        <div class="flex justify-between items-center mb-3 p-2 border-b">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('storage/' . $pessoa->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($pessoa->name)) }}" class="w-8 h-8 rounded-full">
                <a href="{{ route('profile.edit', $pessoa->id) }}" class="hover:underline">
                    {{ $pessoa->name }}
                </a>
            </div>

            <form method="POST" action="{{ route('follow.toggle', $pessoa) }}">
                @csrf
                <button class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Seguir
                </button>
            </form>
        </div>
    @empty
        <p class="text-gray-500">Nenhuma sugestão no momento.</p>
    @endforelse
</x-app-layout>
