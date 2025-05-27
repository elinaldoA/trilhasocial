<x-app-layout>
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <h2 class="text-2xl font-semibold mb-6">Resultados da Busca</h2>

        {{-- Lista de Usuários --}}
        <div class="mb-10">
            <h3 class="text-xl font-bold mb-3">Usuários</h3>
            @if ($users->isEmpty())
                <p class="text-gray-600">Nenhum usuário encontrado.</p>
            @else
                <ul class="space-y-3">
                    @foreach ($users as $user)
                        <li class="flex items-center justify-between bg-white p-4 rounded shadow hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-4">
                                <img
                                    src="{{ asset('storage/' .$user->profile_photo_path ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name)) }}"
                                    alt="Foto de {{ $user->name }}"
                                    class="h-12 w-12 rounded-full object-cover border border-gray-300"
                                />
                                <div>
                                    <h4 class="text-lg font-medium text-gray-800">{{ $user->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('perfil.publico', $user->username) }}" class="text-blue-600 hover:underline">Ver Perfil</a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
