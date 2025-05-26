<x-app-layout>
    <h1 class="text-2xl font-semibold mb-6">
        {{ $user->name }} {{ isset($seguidores) ? 'tem' : 'está seguindo' }}
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
                    <a href="{{ route('profile.edit', $pessoa->id) }}" class="flex items-center space-x-4 hover:underline">
                        <!-- Foto de perfil pequena -->
                        <img
                            src="{{ asset('storage/' . $pessoa->profile_photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode($pessoa->name).'&background=random' )}}"
                            alt="{{ $pessoa->name }}"
                            class="w-10 h-10 rounded-full object-cover"
                        >
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
