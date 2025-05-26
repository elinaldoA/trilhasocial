<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-6 flex gap-8">

        {{-- Feed principal --}}
        <main class="flex-1 space-y-6">

            @foreach ($trails as $trail)
                <article x-data="{ showComments: false, popupCompartilharAberto: false, current: 0 }"
                    class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">

                    {{-- Header --}}
                    <header class="flex items-center px-4 py-3">
                        <img src="{{ asset('storage/' . $trail->user->profile_photo_path ?? 'https://ui-avatars.com/api/?name=' . urlencode($trail->user->name)) }}"
                            alt="Foto do usuário {{ $trail->user->name }}"
                            class="w-10 h-10 rounded-full object-cover border border-gray-300 mr-3" />

                        <div class="flex flex-col flex-1 min-w-0">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $trail->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $trail->location }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Criado por {{ $trail->user->name }} • {{ $trail->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                aria-label="Mais opções">
                                <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="5" cy="12" r="2" />
                                    <circle cx="12" cy="12" r="2" />
                                    <circle cx="19" cy="12" r="2" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                <div class="py-1 text-sm text-gray-700">
                                    @if ($trail->user_id === auth()->id())
                                        <a href="{{ route('trails.edit', $trail) }}"
                                            class="block px-4 py-2 hover:bg-gray-100">Editar</a>
                                        <form method="POST" action="{{ route('trails.destroy', $trail) }}"
                                            onsubmit="return confirm('Excluir esta trilha?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-100">Excluir</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('follow.toggle', $trail->user) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                                @if (auth()->user()->isFollowing($trail->user))
                                                    Deixar de seguir {{ $trail->user->name }}
                                                @else
                                                    Seguir {{ $trail->user->name }}
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </header>

                    {{-- Imagem (carrossel) --}}
                    @if ($trail->images->count())
                        <div
                            class="relative w-full aspect-w-16 aspect-h-9 bg-black select-none overflow-hidden rounded-t-lg">
                            <div class="flex transition-transform duration-500"
                                :style="'transform: translateX(-' + (current * 100) + '%)'">
                                @foreach ($trail->images as $image)
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem da trilha"
                                        class="w-full h-auto object-cover flex-shrink-0" />
                                @endforeach
                            </div>

                            @if ($trail->images->count() > 1)
                                {{-- Navegação esquerda/direita --}}
                                <button
                                    @click="current = (current - 1 + {{ $trail->images->count() }}) % {{ $trail->images->count() }}"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white rounded-full p-1 focus:outline-none">
                                    ‹
                                </button>
                                <button @click="current = (current + 1) % {{ $trail->images->count() }}"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white rounded-full p-1 focus:outline-none">
                                    ›
                                </button>

                                {{-- Indicadores --}}
                                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-1">
                                    @foreach ($trail->images as $index => $image)
                                        <span :class="current === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                                            class="w-2 h-2 rounded-full cursor-pointer"
                                            @click="current = {{ $index }}"></span>
                                    @endforeach
                                </div>
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
                                class="text-blue-600 hover:underline mt-1 focus:outline-none">
                                <span x-text="expanded ? 'Mostrar menos' : 'Mostrar mais'"></span>
                            </button>
                        </div>

                        @php
                            $hours = intdiv($trail->avg_time, 60);
                            $minutes = $trail->avg_time % 60;
                        @endphp

                        <div
                            class="flex flex-wrap gap-4 text-xs md:text-sm text-gray-600 font-semibold items-center mt-2">
                            <div>🏞️ Distância: {{ $trail->distance }} km</div>
                            <div>⏱️ Tempo médio:
                                {{ $hours ? $hours . 'h ' : '' }}{{ $minutes ? $minutes . 'min' : '' }}</div>
                            <div>Nível: {{ ucfirst($trail->difficulty) }}</div>
                        </div>
                    </div>

                    {{-- Rodapé com interações --}}
                    <footer class="px-4 py-2 flex justify-between items-center text-gray-600">
                        <div class="flex space-x-4">

                            {{-- Curtir --}}
                            <form method="POST" action="{{ route('trails.toggle', $trail) }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-1 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 {{ $trail->likes->contains('user_id', auth()->id()) ? 'fill-red-500 text-red-500' : 'text-gray-500 hover:text-red-500' }}"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                                    </svg>
                                    <span class="text-sm select-none">{{ $trail->likes->count() }}</span>
                                </button>
                            </form>

                            {{-- Comentário --}}
                            <button @click="showComments = !showComments"
                                class="flex items-center space-x-1 focus:outline-none text-gray-500 hover:text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.363-1.02L3 20l1.02-4.363A9.77 9.77 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="text-sm select-none">{{ $trail->comments->count() }}</span>
                            </button>
                        </div>

                        {{-- Compartilhar --}}
                        <button @click="popupCompartilharAberto = true"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 12v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 6l-4-4-4 4M12 2v14" />
                            </svg>
                        </button>
                    </footer>

                    {{-- Comentários --}}
                    <div x-show="showComments" x-transition
                        class="border-t border-gray-200 px-4 py-3 bg-gray-50 max-h-64 overflow-y-auto">
                        <form method="POST" action="{{ route('comments.store') }}" class="mb-4">
                            @csrf
                            <input type="hidden" name="trail_id" value="{{ $trail->id }}">
                            <textarea name="body" rows="3" required
                                class="w-full rounded border border-gray-300 p-2 resize-none focus:outline-blue-500"
                                placeholder="Escreva um comentário..."></textarea>
                            <div class="text-right mt-2">
                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm">Comentar</button>
                            </div>
                        </form>

                        @foreach ($trail->comments->whereNull('parent_id') as $comment)
                            <div class="mb-4">
                                <div><strong>{{ $comment->user->name }}:</strong> {{ $comment->body }}</div>
                                <div x-data="{ showReplyForm: false }" class="ml-4 mt-1">
                                    <button @click="showReplyForm = !showReplyForm"
                                        class="text-xs text-blue-500 hover:underline">Responder</button>
                                    <div x-show="showReplyForm" x-transition class="mt-2">
                                        <form method="POST" action="{{ route('comments.store') }}">
                                            @csrf
                                            <input type="hidden" name="trail_id" value="{{ $trail->id }}">
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <textarea name="body" rows="2" required
                                                class="w-full rounded border border-gray-300 p-2 resize-none focus:outline-blue-500"
                                                placeholder="Escreva uma resposta..."></textarea>
                                            <div class="text-right mt-2">
                                                <button type="submit"
                                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Enviar
                                                    resposta</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @foreach ($comment->replies as $reply)
                                    <div class="ml-6 mt-2 border-l border-gray-300 pl-3">
                                        <strong>{{ $reply->user->name }}:</strong> {{ $reply->body }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- Popup Compartilhar --}}
                    <div x-show="popupCompartilharAberto" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
                        <div class="bg-white p-6 rounded-2xl shadow-2xl w-96 relative animate-fade-in-up">
                            <button @click="popupCompartilharAberto = false"
                                class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-2xl font-bold focus:outline-none">&times;</button>

                            <h2 class="text-xl font-semibold mb-5 text-gray-800">Compartilhar esta trilha</h2>

                            <div class="flex flex-col space-y-4 text-base">
                                @php
                                    $url = urlencode(route('trails.show', $trail));
                                    $title = urlencode($trail->title ?? 'Confira essa trilha!');
                                @endphp

                                <a href="https://wa.me/?text={{ $title }}%20{{ $url }}"
                                    target="_blank"
                                    class="flex items-center gap-2 text-green-600 hover:text-green-700 transition">
                                    <i class="fab fa-whatsapp text-xl"></i> WhatsApp
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}"
                                    target="_blank"
                                    class="flex items-center gap-2 text-blue-700 hover:text-blue-800 transition">
                                    <i class="fab fa-facebook text-xl"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ $title }}&url={{ $url }}"
                                    target="_blank"
                                    class="flex items-center gap-2 text-blue-400 hover:text-blue-500 transition">
                                    <i class="fab fa-twitter text-xl"></i> Twitter
                                </a>
                                <a href="https://instagram.com/?text={{ $title }}&url={{ $url }}"
                                    target="_blank"
                                    class="flex items-center gap-2 text-purple-400 hover:text-purplue-500 transition">
                                    <i class="fas fa-instagram text-xl"></i> Instagram
                                </a>
                                <button
                                    @click="navigator.clipboard.writeText('{{ route('trails.show', $trail) }}').then(() => alert('Link copiado!'))"
                                    class="text-gray-700 hover:text-gray-900 focus:outline-none">
                                    Copiar Link
                                </button>
                            </div>
                        </div>
                    </div>

                </article>
            @endforeach
        </main>

        {{-- Sidebar fixa --}}
        <aside class="hidden lg:block w-80 sticky top-20 h-[calc(100vh-5rem)] overflow-y-auto px-2">
            <div class="bg-white rounded-xl border border-gray-300 shadow p-4 mb-6">
                <h2 class="font-semibold text-lg text-gray-800 mb-4">Sugestões para você</h2>
                @foreach ($sugestoes as $sugestao)
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('storage/' . $sugestao->profile_photo_path ?? 'https://ui-avatars.com/api/?name=' . urlencode($sugestao->name)) }}"
                                alt="{{ $sugestao->name }}"
                                class="w-10 h-10 rounded-full object-cover border border-gray-300" />
                            <div>
                                <p class="font-semibold text-gray-900 text-sm truncate max-w-[150px]">
                                    {{ $sugestao->name }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-[150px]">@
                                    {{ $sugestao->username ?? 'user' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('follow.toggle', $sugestao) }}">
                            @csrf
                            <button type="submit"
                                class="text-sm text-blue-600 font-semibold hover:underline focus:outline-none">Seguir</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
</x-app-layout>
