<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="relative">
                <img src="{{ $user->coverPhotoUrl() }}" alt="Capa" class="w-full h-52 sm:h-60 object-cover">

                <div class="absolute -bottom-12 left-6">
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Avatar"
                        class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover">
                </div>
            </div>

            <div class="pt-16 px-6 pb-6">
                <h2 class="text-3xl font-extrabold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mb-2">{{ '@' . $user->username }}</p>
                <div class="flex space-x-6 text-sm text-gray-700 mb-4">
                    <div class="flex items-center space-x-1">
                        <span class="font-semibold">{{ $user->followers()->count() }}</span>
                        <span>seguidores</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="font-semibold">{{ $user->following()->count() }}</span>
                        <span>seguindo</span>
                    </div>
                </div>

                @if ($user->bio)
                    <p class="mb-4 text-gray-700 leading-relaxed">{{ $user->bio }}</p>
                @endif

                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                    @if ($user->location)
                        <div class="flex items-center space-x-1">
                            <!-- Icone localização -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 11c1.656 0 3-1.344 3-3S13.656 5 12 5 9 6.344 9 8s1.344 3 3 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21s-6-5.686-6-10a6 6 0 0112 0c0 4.314-6 10-6 10z" />
                            </svg>
                            <span>{{ $user->location }}</span>
                        </div>
                    @endif
                    @if ($user->website)
                        <div class="flex items-center space-x-1">
                            <!-- Icone site/externo -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14 3h7v7m0 0L10 21l-7-7 11-11z" />
                            </svg>
                            <a href="{{ $user->website }}" target="_blank"
                                class="text-blue-500 hover:underline truncate max-w-xs">{{ $user->website }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-2xl font-semibold mb-4">Trilhas de {{ $user->name }}</h3>

            {{-- Tabs --}}
            <div class="mb-6 border-b border-gray-300">
                <nav class="flex space-x-4" aria-label="Tabs">
                    <button id="tab-images"
                        class="py-2 px-4 flex items-center space-x-2 text-gray-700 border-b-2 border-blue-600 font-semibold focus:outline-none"
                        onclick="showTab('images')">
                        <!-- Icone imagem -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l3 3 4-4" />
                        </svg>
                        <span>Imagens</span>
                    </button>
                    <button id="tab-videos"
                        class="py-2 px-4 flex items-center space-x-2 text-gray-500 hover:text-gray-700 border-b-2 border-transparent focus:outline-none"
                        onclick="showTab('videos')">
                        <!-- Icone vídeo -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h7a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
                        </svg>
                        <span>Vídeos</span>
                    </button>
                </nav>
            </div>

            {{-- Conteúdo da aba Imagens --}}
            <div id="content-images">
                @php $hasAnyImage = false; @endphp
                @foreach ($trails as $trail)
                    @if ($trail->images && $trail->images->count())
                        @php $hasAnyImage = true; @endphp
                        <div class="bg-white shadow rounded-lg mb-6 p-4">
                            <h4 class="text-xl font-bold mb-2">{{ $trail->name }}</h4>
                            <p class="text-gray-700 mb-2">{{ $trail->description }}</p>

                            <div class="flex flex-wrap gap-6 text-sm text-gray-600 mb-4">
                                <div class="flex items-center space-x-1">
                                    <!-- Icone localização -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.656 0 3-1.344 3-3S13.656 5 12 5 9 6.344 9 8s1.344 3 3 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21s-6-5.686-6-10a6 6 0 0112 0c0 4.314-6 10-6 10z" />
                                    </svg>
                                    <span>{{ $trail->location }}</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Icone dificuldade -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.121 17.804A9 9 0 1112 21a9.004 9.004 0 01-6.879-3.196z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 7v5l4 2" />
                                    </svg>
                                    <span>{{ ucfirst($trail->difficulty) }}</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Icone distância -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8c-2 0-6 1-6 5v1h12v-1c0-4-4-5-6-5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4" />
                                    </svg>
                                    <span>{{ $trail->distance }} km</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Icone tempo médio -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6l4 2" />
                                    </svg>
                                    <span>{{ $trail->avg_time }} min</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($trail->images as $image)
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem da trilha"
                                        class="rounded-lg object-cover w-full aspect-square hover:brightness-90 transition duration-300 cursor-pointer" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
                @if (!$hasAnyImage)
                    <p class="text-gray-500 italic">Esse usuário ainda não publicou imagens em trilhas.</p>
                @endif
            </div>

            {{-- Conteúdo da aba Vídeos --}}
            <div id="content-videos" class="hidden">
                @php $hasAnyVideo = false; @endphp
                @foreach ($trails as $trail)
                    @if ($trail->videos && $trail->videos->count())
                        @php $hasAnyVideo = true; @endphp
                        <div class="bg-white shadow rounded-lg mb-6 p-4">
                            <h4 class="text-xl font-bold mb-2">{{ $trail->name }}</h4>
                            <p class="text-gray-700 mb-2">{{ $trail->description }}</p>

                            <div class="flex flex-wrap gap-6 text-sm text-gray-600 mb-4">
                                <div class="flex items-center space-x-1">
                                    <!-- Icone localização -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 11c1.656 0 3-1.344 3-3S13.656 5 12 5 9 6.344 9 8s1.344 3 3 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21s-6-5.686-6-10a6 6 0 0112 0c0 4.314-6 10-6 10z" />
                                    </svg>
                                    <span>{{ $trail->location }}</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Icone dificuldade -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.121 17.804A9 9 0 1112 21a9.004 9.004 0 01-6.879-3.196z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 7v5l4 2" />
                                    </svg>
                                    <span>{{ ucfirst($trail->difficulty) }}</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Icone distância -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8c-2 0-6 1-6 5v1h12v-1c0-4-4-5-6-5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4" />
                                    </svg>
                                    <span>{{ $trail->distance }} km</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Icone tempo médio -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6l4 2" />
                                    </svg>
                                    <span>{{ $trail->avg_time }} min</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($trail->videos as $video)
                                    <video controls class="rounded-lg w-full aspect-video bg-black">
                                        <source src="{{ asset('storage/' . $video->path) }}" type="video/mp4">
                                        Seu navegador não suporta o elemento de vídeo.
                                    </video>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
                @if (!$hasAnyVideo)
                    <p class="text-gray-500 italic">Esse usuário ainda não publicou vídeos em trilhas.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showTab(tab) {
            const imagesTab = document.getElementById('tab-images');
            const videosTab = document.getElementById('tab-videos');

            const contentImages = document.getElementById('content-images');
            const contentVideos = document.getElementById('content-videos');

            if (tab === 'images') {
                imagesTab.classList.add('border-blue-600', 'text-gray-700', 'font-semibold');
                imagesTab.classList.remove('text-gray-500');
                videosTab.classList.remove('border-blue-600', 'text-gray-700', 'font-semibold');
                videosTab.classList.add('text-gray-500');

                contentImages.classList.remove('hidden');
                contentVideos.classList.add('hidden');
            } else {
                videosTab.classList.add('border-blue-600', 'text-gray-700', 'font-semibold');
                videosTab.classList.remove('text-gray-500');
                imagesTab.classList.remove('border-blue-600', 'text-gray-700', 'font-semibold');
                imagesTab.classList.add('text-gray-500');

                contentVideos.classList.remove('hidden');
                contentImages.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
