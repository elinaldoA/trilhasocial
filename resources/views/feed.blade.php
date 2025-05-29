<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-6 flex gap-8">

        {{-- Feed principal --}}
        <main class="flex-1 space-y-8 bg-gray-50">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false;
                    setTimeout(() => location.reload(), 500); }, 4000)" x-transition
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false;
                    setTimeout(() => location.reload(), 500); }, 4000)" x-transition
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @foreach ($trails as $trail)
                <article x-data="{ showComments: false, popupCompartilharAberto: false, current: 0 }"
                    class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl transition-all duration-300 p-6 space-y-4">

                    {{-- Header --}}
                    <header class="flex items-center gap-4">
                        <a href="{{ route('perfil.publico', $trail->user->username) }}">
                            <img src="{{ $trail->user->profile_photo_path ? asset('storage/' . $trail->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($trail->user->name) }}"
                                alt="Foto de {{ $trail->user->name }}"
                                class="w-12 h-12 rounded-full object-cover ring-2 ring-primary-500" />
                        </a>

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

                    @if ($trail->images->count())
                        {{-- Carrossel de imagens (mantido igual) --}}
                        <div x-data="{ current: 0 }"
                            class="relative w-full aspect-w-16 aspect-h-9 bg-black select-none overflow-hidden rounded-t-lg">
                            {{-- Container de imagens --}}
                            <div class="flex transition-transform duration-500"
                                :style="'transform: translateX(-' + (current * 100) + '%)'">
                                @foreach ($trail->images as $image)
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem da trilha"
                                        class="w-full h-auto object-cover flex-shrink-0" />
                                @endforeach
                            </div>

                            @if ($trail->images->count() > 1)
                                {{-- Botões de navegação --}}
                                <button
                                    @click="current = (current - 1 + {{ $trail->images->count() }}) % {{ $trail->images->count() }}"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white rounded-full p-1">
                                    ‹
                                </button>
                                <button @click="current = (current + 1) % {{ $trail->images->count() }}"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white rounded-full p-1">
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
                    @elseif ($trail->videos->count())
                        {{-- Carrossel de vídeos --}}
                        <div x-data="{ current: 0, playing: false }"
                            class="relative w-full aspect-w-16 aspect-h-9 bg-black select-none overflow-hidden rounded-t-lg">

                            {{-- Container de vídeos --}}
                            <div class="flex transition-transform duration-500"
                                :style="'transform: translateX(-' + (current * 100) + '%)'">
                                @foreach ($trail->videos as $video)
                                    <div class="w-full h-full flex-shrink-0 relative">
                                        <video x-ref="video{{ $loop->index }}" class="w-full h-full object-cover"
                                            :muted="!playing" playsinline
                                            @click="playing = !playing; $refs.video{{ $loop->index }}.paused ? $refs.video{{ $loop->index }}.play() : $refs.video{{ $loop->index }}.pause()">
                                            <source src="{{ asset('storage/' . $video->path) }}" type="video/mp4" />
                                            Seu navegador não suporta vídeos HTML5.
                                        </video>
                                        <div x-show="!playing && $refs.video{{ $loop->index }}.paused"
                                            class="absolute inset-0 flex items-center justify-center">
                                            <button @click="playing = true; $refs.video{{ $loop->index }}.play()"
                                                class="bg-black bg-opacity-50 text-white rounded-full p-4 hover:bg-opacity-70 transition">
                                                ▶
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($trail->videos->count() > 1)
                                {{-- Botões de navegação --}}
                                <button
                                    @click="current = (current - 1 + {{ $trail->videos->count() }}) % {{ $trail->videos->count() }}; playing = false;"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white rounded-full p-1">
                                    ‹
                                </button>
                                <button
                                    @click="current = (current + 1) % {{ $trail->videos->count() }}; playing = false;"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-60 text-white rounded-full p-1">
                                    ›
                                </button>

                                {{-- Indicadores --}}
                                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-1">
                                    @foreach ($trail->videos as $index => $video)
                                        <span :class="current === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                                            class="w-2 h-2 rounded-full cursor-pointer"
                                            @click="current = {{ $index }}; playing = false;"></span>
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
                    <footer
                        class="px-4 py-3 flex justify-between items-center text-gray-600 bg-white border-t border-gray-200"
                        x-data="{ popupDenunciaAberto: false }">

                        <div class="flex space-x-6 items-center">
                            {{-- Curtir --}}
                            <form method="POST" action="{{ route('trails.toggle', $trail) }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-1 focus:outline-none"
                                    aria-label="Curtir trilha">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 transition-colors duration-200 {{ $trail->likes->contains('user_id', auth()->id()) ? 'fill-red-500 text-red-500' : 'text-gray-500 hover:text-red-500' }}"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" role="img"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                                    </svg>
                                    <span class="text-sm select-none">{{ $trail->likes->count() }}</span>
                                </button>
                            </form>

                            {{-- Comentário --}}
                            <button @click="showComments = !showComments"
                                class="flex items-center space-x-1 focus:outline-none text-gray-500 hover:text-blue-600 transition-colors duration-200"
                                aria-expanded="false" :aria-expanded="showComments.toString()"
                                aria-label="Mostrar/ocultar comentários">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" role="img"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.363-1.02L3 20l1.02-4.363A9.77 9.77 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="text-sm select-none">{{ $trail->comments->count() }}</span>
                            </button>
                        </div>

                        <div class="flex items-center space-x-6">
                            {{-- Compartilhar --}}
                            <button @click="popupCompartilharAberto = true"
                                class="text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200"
                                aria-label="Compartilhar trilha">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" role="img"
                                    aria-hidden="true">
                                    <path
                                        d="M18 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM6 14a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM18 20a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM8.6 13.5l6.8 3.98M15.4 6.5l-6.8 3.98" />
                                </svg>
                            </button>

                            {{-- Denunciar --}}
                            <button @click="popupDenunciaAberto = true"
                                class="text-red-500 hover:text-red-700 focus:outline-none transition-colors duration-200"
                                aria-label="Denunciar trilha">
                                Denunciar
                            </button>
                        </div>

                        {{-- Modal Denúncia --}}
                        <div x-show="popupDenunciaAberto" x-transition.opacity
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                            style="display: none;" @keydown.escape.window="popupDenunciaAberto = false"
                            @click.away="popupDenunciaAberto = false" role="dialog" aria-modal="true"
                            aria-labelledby="modal-title-denuncia">
                            <div
                                class="bg-white rounded-lg p-6 w-full max-w-md mx-4 shadow-lg border border-gray-200 overflow-auto max-h-[90vh]">
                                <h3 id="modal-title-denuncia" class="text-lg font-semibold mb-4 text-gray-900">
                                    Denunciar esta trilha</h3>
                                <form method="POST" action="{{ route('trails.report', $trail) }}">
                                    @csrf
                                    <label for="reason" class="block mb-2 font-medium text-gray-700">Motivo <span
                                            class="text-red-500">*</span></label>
                                    <select id="reason" name="reason" required
                                        class="w-full mb-4 rounded border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <option value="" disabled selected>Selecione um motivo</option>
                                        <option value="conteudo_inapropriado">Conteúdo inapropriado</option>
                                        <option value="spam">Spam</option>
                                        <option value="direitos_autorais">Violação de direitos autorais</option>
                                        <option value="outro">Outro</option>
                                    </select>

                                    <label for="details" class="block mb-2 font-medium text-gray-700">Detalhes
                                        (opcional)</label>
                                    <textarea id="details" name="details" rows="4" placeholder="Descreva o problema..."
                                        class="w-full rounded border border-gray-300 p-2 resize-none focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" @click="popupDenunciaAberto = false"
                                            class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600">
                                            Enviar denúncia
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- (Opcional) Modal Compartilhar (vazio para implementar) --}}
                        <div x-show="popupCompartilharAberto" x-transition.opacity
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                            style="display: none;" @keydown.escape.window="popupCompartilharAberto = false"
                            @click.away="popupCompartilharAberto = false" role="dialog" aria-modal="true"
                            aria-labelledby="modal-title-compartilhar">
                            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 shadow-lg border border-gray-200">
                                <h3 id="modal-title-compartilhar" class="text-lg font-semibold mb-4 text-gray-900">
                                    Compartilhar trilha</h3>
                                <!-- Conteúdo do modal compartilhar aqui -->
                                <button @click="popupCompartilharAberto = false"
                                    class="mt-4 px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                    Fechar
                                </button>
                            </div>
                        </div>

                    </footer>

                    <!-- Alpine.js -->
                    <script src="//unpkg.com/alpinejs" defer></script>


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
                        <div
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden relative flex flex-col animate-fade-in-up">

                            {{-- Cabeçalho --}}
                            <div class="px-4 py-3 border-b relative text-center font-semibold text-gray-800">
                                Compartilhar com
                                <button @click="popupCompartilharAberto = false"
                                    class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-2xl font-bold focus:outline-none">&times;</button>
                            </div>

                            {{-- Formulário de envio para seguidores --}}
                            <form method="POST" action="{{ route('trails.share', $trail) }}"
                                class="flex-1 flex flex-col">
                                @csrf

                                <div class="px-4 pt-3 pb-2 border-b text-sm text-gray-600">
                                    Selecione seguidores para enviar: <strong>"{{ $trail->description }}"</strong>
                                </div>

                                <div class="flex-1 overflow-y-auto px-4">
                                    @foreach (auth()->user()->followers as $follower)
                                        <div class="flex items-center justify-between py-3 border-b">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $follower->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($follower->name) }}"
                                                    alt="{{ $follower->name }}"
                                                    class="w-10 h-10 rounded-full object-cover">
                                                <span class="text-gray-800">{{ $follower->name }}</span>
                                            </div>
                                            <label>
                                                <input type="checkbox" name="followers[]"
                                                    value="{{ $follower->id }}" class="peer hidden">
                                                <span
                                                    class="px-3 py-1 border rounded-full text-sm text-blue-600 border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white cursor-pointer">
                                                    Selecionar
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Botões de ação --}}
                                <div class="p-4 border-t flex justify-between items-center bg-white gap-2 flex-wrap">
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg">
                                        Enviar para seguidores
                                    </button>
                                    <br />
                                    <br />
                                    <br />
                                    {{-- Redes sociais externas --}}
                                    @php
                                        $url = urlencode(route('trails.show', $trail));
                                        $title = urlencode($trail->title ?? 'Confira essa trilha!');
                                    @endphp
                                    <div class="flex items-center gap-3 text-sm flex-wrap">
                                        <a href="https://wa.me/?text={{ $title }}%20{{ $url }}"
                                            target="_blank"
                                            class="text-green-600 hover:text-green-700 flex items-center gap-1">
                                            <i class="fab fa-whatsapp text-lg"></i> WhatsApp |
                                        </a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}"
                                            target="_blank"
                                            class="text-blue-700 hover:text-blue-800 flex items-center gap-1">
                                            <i class="fab fa-facebook text-lg"></i> Facebook |
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?text={{ $title }}&url={{ $url }}"
                                            target="_blank"
                                            class="text-blue-400 hover:text-blue-500 flex items-center gap-1">
                                            <i class="fab fa-twitter text-lg"></i> Twitter |
                                        </a>
                                        <a href="https://instagram.com/?text={{ $title }}&url={{ $url }}"
                                            target="_blank"
                                            class="text-purple-500 hover:text-purple-600 flex items-center gap-1">
                                            <i class="fab fa-instagram text-lg"></i> Instagram
                                        </a>
                                    </div>
                                </div>
                            </form>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const videos = document.querySelectorAll(".video-autoplay-with-audio");

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target;

                    if (entry.isIntersecting) {
                        // Ativa som e inicia reprodução se visível
                        if (video.dataset.audio === "true") {
                            video.muted = false;
                            video.play().catch(err => {
                                console.warn("Autoplay bloqueado:", err);
                            });
                        }
                    } else {
                        // Pausa o vídeo quando sai da tela
                        video.pause();
                    }
                });
            }, {
                threshold: 0.5
            });

            videos.forEach(video => observer.observe(video));
        });
    </script>

</x-app-layout>
