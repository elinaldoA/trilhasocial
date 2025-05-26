<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="relative">
                <img src="{{ $user->coverPhotoUrl() }}" alt="Capa" class="w-full h-60 object-cover">
                <div class="absolute -bottom-12 left-6">
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-white shadow">
                </div>
            </div>
            <div class="pt-16 px-6 pb-6">
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ '@' . $user->username }}</p>

                @if($user->bio)
                    <p class="mt-2 text-gray-700">{{ $user->bio }}</p>
                @endif

                <div class="mt-3 text-sm text-gray-600">
                    @if($user->location)
                        <span class="inline-block mr-4">📍 {{ $user->location }}</span>
                    @endif
                    @if($user->website)
                        <span class="inline-block">🌐 <a href="{{ $user->website }}" target="_blank" class="text-blue-500 hover:underline">{{ $user->website }}</a></span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-xl font-semibold mb-4">Trilhas de {{ $user->name }}</h3>

            @forelse($trails as $trail)
                <div class="bg-white shadow rounded-lg p-4 mb-4">
                    <p class="text-gray-800">{{ $trail->content }}</p>

                    @if($trail->images)
                        <div class="mt-2">
                            @foreach($trail->images as $image)
                                <img src="{{ asset('storage/' . $image->path) }}" class="rounded-lg mb-2 w-full max-h-96 object-cover">
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">Esse usuário ainda não publicou nenhuma trilha.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
