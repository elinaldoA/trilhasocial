<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Feed</h1>
            <a href="{{ route('trails.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                +
            </a>
        </div>

        @foreach ($trails as $trail)
            <article x-data="{ showComments: false }"
                     class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">

                {{-- Header --}}
                <header class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <img src="{{ $trail->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($trail->user->name) }}"
                             alt="Foto do usuário"
                             class="h-12 w-12 rounded-full object-cover mr-4 border border-gray-300" />
                        <div class="flex flex-col">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $trail->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $trail->location }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                Criado por {{ $trail->user->name }} • {{ $trail->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Menu três pontinhos estilo Instagram --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="p-2 rounded-full hover:bg-gray-200 focus:outline-none"
                                aria-label="Mais opções">
                            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="5" cy="12" r="2" />
                                <circle cx="12" cy="12" r="2" />
                                <circle cx="19" cy="12" r="2" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                             class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                            <div class="py-1 text-sm text-gray-700" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="{{ route('trails.edit', $trail) }}" class="block px-4 py-2 hover:bg-gray-100" role="menuitem">Editar</a>

                                <form method="POST" action="{{ route('trails.destroy', $trail) }}"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta trilha?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100" role="menuitem">
                                        Excluir
                                    </button>
                                </form>

                                <button @click="open = false; abrirPopupCompartilhar()"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100" role="menuitem">
                                    Compartilhar
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Imagens --}}
                @if ($trail->images->count())
                    <div x-data="{ current: 0 }"
                         class="relative w-full overflow-hidden aspect-[4/5] md:aspect-[4/3] bg-black rounded-lg border border-gray-200">
                        <div class="flex transition-transform duration-500"
                             :style="'transform: translateX(-' + (current * 100) + '%)'">
                            @foreach ($trail->images as $image)
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem da trilha"
                                     class="w-full h-full object-cover flex-shrink-0" />
                            @endforeach
                        </div>

                        @if ($trail->images->count() > 1)
                            <button @click="current = (current - 1 + {{ $trail->images->count() }}) % {{ $trail->images->count() }}"
                                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white px-3 py-2 rounded-r">
                                ‹
                            </button>
                            <button @click="current = (current + 1) % {{ $trail->images->count() }}"
                                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white px-3 py-2 rounded-l">
                                ›
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Conteúdo --}}
                <div class="px-4 py-3">
                    <div x-data="{ expanded: false }" class="mt-2 text-gray-700 dark:text-gray-300">
                        <template x-if="!expanded">
                            <p>{{ Str::limit($trail->description, 100) }}...</p>
                        </template>
                        <template x-if="expanded">
                            <p>{{ $trail->description }}</p>
                        </template>
                        <button @click="expanded = !expanded"
                                class="text-blue-600 hover:underline mt-1 focus:outline-none"
                                aria-expanded="false" x-bind:aria-expanded="expanded.toString()">
                            <span x-text="expanded ? 'Mostrar menos' : 'Mostrar mais'"></span>
                        </button>
                    </div>

                    @php
                        $hours = intdiv($trail->avg_time, 60);
                        $minutes = $trail->avg_time % 60;
                    @endphp

                    <div class="flex flex-wrap gap-4 text-xs md:text-sm text-gray-600 font-semibold items-center mt-3">
                        <div>🏞️ Distância: {{ $trail->distance }} km</div>
                        <div>⏱️ Tempo médio:
                            @if ($hours > 0) {{ $hours }}h @endif
                            @if ($minutes > 0) {{ $minutes }}min @endif
                        </div>
                        <div>Nível: {{ ucfirst($trail->difficulty) }}</div>
                    </div>
                </div>

                {{-- Rodapé com interações --}}
                <footer class="flex justify-between items-center px-4 py-2 border-t border-gray-100 text-gray-600 text-sm">
                    <div class="flex space-x-6 items-center">
                        {{-- Curtir --}}
                        <form method="POST" action="{{ route('trails.toggle', $trail) }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-1 group transition">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-5 w-5 {{ $trail->likes->contains('user_id', auth()->id()) ? 'fill-red-500 text-red-500' : 'text-gray-500 group-hover:text-red-500' }}"
                                     fill="{{ $trail->likes->contains('user_id', auth()->id()) ? 'currentColor' : 'none' }}"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                                </svg>
                                <span>{{ $trail->likes->count() }}</span>
                            </button>
                        </form>

                        {{-- Comentar --}}
                        <button @click="showComments = !showComments" class="flex items-center space-x-1 group transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-gray-500 group-hover:text-blue-600" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.363-1.02L3 20l1.02-4.363A9.77 9.77 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>{{ $trail->comments->count() }}</span>
                        </button>
                    </div>

                    {{-- Visualizações --}}
                    <div class="flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>{{ $trail->views_count }}</span>
                    </div>
                </footer>

                {{-- Comentários (visíveis quando showComments=true) --}}
                <div x-show="showComments" x-transition class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    @foreach ($trail->comments as $comment)
                        <div class="mb-3">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $comment->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}"
                                     alt="Avatar do usuário" class="w-8 h-8 rounded-full object-cover border border-gray-300" />
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <p class="mt-1 text-gray-700 text-sm">{{ $comment->content }}</p>
                        </div>
                    @endforeach

                    {{-- Formulário de novo comentário --}}
                    <form action="{{ route('comments.store', $trail) }}" method="POST" class="mt-4">
                        @csrf
                        <textarea name="content" rows="2" required
                                  class="w-full rounded border border-gray-300 p-2 text-sm resize-none"
                                  placeholder="Adicione um comentário..."></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">
                                Comentar
                            </button>
                        </div>
                    </form>
                </div>
            </article>
        @endforeach

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $trails->links() }}
        </div>
    </div>

    {{-- Modal/Popup Compartilhar - pode ser implementado com Alpine.js --}}
    <script>
        function abrirPopupCompartilhar() {
            alert('Funcionalidade de compartilhamento ainda não implementada.');
        }
    </script>
</x-app-layout>
