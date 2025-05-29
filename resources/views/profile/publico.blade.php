<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="relative">
                <img src="{{ $user->coverPhotoUrl() }}" alt="Capa" class="w-full h-52 sm:h-60 object-cover">

                <div class="absolute -bottom-12 left-6">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                            alt="Avatar"
                            class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}"
                            alt="Avatar"
                            class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover">
                    @endif
                </div>
            </div>

            <div class="pt-16 px-6 pb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-3xl font-extrabold">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500 mb-2">{{ '@' . $user->username }}</p>
                    </div>
                    @if (auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Editar perfil
                        </a>
                    @endif
                </div>
                <div class="flex space-x-6 text-sm text-gray-700 mb-4">
                    <div class="flex items-center space-x-1">
                        <span class="font-semibold">{{ $user->trails()->count() }}</span>
                        <span>Trilhas</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="font-semibold">{{ $user->followers()->count() }}</span>
                        <span>Seguidores</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="font-semibold">{{ $user->following()->count() }}</span>
                        <span>Seguindo</span>
                    </div>
                </div>

                @if ($user->bio)
                    <p class="mb-4 text-gray-700 leading-relaxed">{{ $user->bio }}</p>
                @endif

                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                    @if ($user->location)
                        <div class="flex items-center space-x-1">
                            <!-- Ícone localização -->
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
                            <!-- Ícone site/externo -->
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

            @php $hasAnyTrail = false; @endphp
            <div class="relative border-l-4 border-blue-500 ml-6 mt-12">
                @foreach ($trails as $trail)
                    @php
                        $hasMedia =
                            ($trail->images && $trail->images->count()) || ($trail->videos && $trail->videos->count());
                    @endphp

                    @if ($hasMedia)
                        @php $hasAnyTrail = true; @endphp
                        <div class="relative mb-10 pl-8">
                            <!-- Ponto da timeline com data -->
                            <div class="absolute left-[-26px] top-2">
                                <div
                                    class="w-14 h-14 bg-blue-600 rounded-full border-4 border-white shadow flex items-center justify-center text-[10px] text-white font-semibold text-center leading-tight px-1">
                                    {{ \Carbon\Carbon::parse($trail->created_at)->format('d/m') }}
                                </div>
                            </div>

                            <div class="bg-white shadow-md rounded-lg p-5">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-xl font-bold text-blue-700">{{ $trail->name }}</h4>
                                </div>

                                <p class="text-gray-700 mb-2">{{ $trail->description }}</p>

                                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 11c1.656 0 3-1.344 3-3S13.656 5 12 5 9 6.344 9 8s1.344 3 3 3z" />
                                            <path d="M12 21s-6-5.686-6-10a6 6 0 0112 0c0 4.314-6 10-6 10z" />
                                        </svg>
                                        <span>{{ $trail->location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M5.121 17.804A9 9 0 1112 21a9.004 9.004 0 01-6.879-3.196z" />
                                            <path d="M12 7v5l4 2" />
                                        </svg>
                                        <span>{{ ucfirst($trail->difficulty) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M12 8c-2 0-6 1-6 5v1h12v-1c0-4-4-5-6-5z" />
                                            <path d="M12 8v4" />
                                        </svg>
                                        <span>{{ $trail->distance }} km</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        <span>
                                            @php
                                                $hours = floor($trail->avg_time / 60);
                                                $minutes = $trail->avg_time % 60;
                                            @endphp

                                            @if ($hours > 0)
                                                {{ $hours }}h @if ($minutes > 0)
                                                    {{ $minutes }}min
                                                @endif
                                            @else
                                                {{ $minutes }}min
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Carousel Imagens --}}
                                @if ($trail->images && $trail->images->count())
                                    <div class="swiper w-full rounded-lg mb-4">
                                        <div class="swiper-wrapper">
                                            @foreach ($trail->images as $image)
                                                <div class="swiper-slide">
                                                    <img src="{{ asset('storage/' . $image->path) }}"
                                                        alt="Imagem da trilha"
                                                        class="object-cover w-full h-64 rounded-lg" />
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination"></div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                    </div>
                                @endif

                                {{-- Carousel Videos --}}
                                @if ($trail->videos && $trail->videos->count())
                                    <div class="swiper w-full rounded-lg mb-4">
                                        <div class="swiper-wrapper">
                                            @foreach ($trail->videos as $video)
                                                <div class="swiper-slide">
                                                    <video controls class="w-full h-64 bg-black rounded-lg">
                                                        <source src="{{ asset('storage/' . $video->path) }}"
                                                            type="video/mp4">
                                                        Seu navegador não suporta o elemento de vídeo.
                                                    </video>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination"></div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                @if (!$hasAnyTrail)
                    <p class="text-gray-500 italic pl-8">Esse usuário ainda não publicou imagens ou vídeos em trilhas.
                    </p>
                @endif
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.swiper').forEach((el) => {
                new Swiper(el, {
                    loop: true,
                    navigation: {
                        nextEl: el.querySelector('.swiper-button-next'),
                        prevEl: el.querySelector('.swiper-button-prev'),
                    },
                    pagination: {
                        el: el.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                });
            });
        });
    </script>
</x-app-layout>
