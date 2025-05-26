<x-app-layout>
    <x-slot name="header">
        Mensagens
    </x-slot>

    <div class="flex h-[600px] bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Lista de usuários à esquerda -->
        <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-lg text-gray-900 dark:text-gray-100">
                Contatos
            </div>

            @foreach ($users as $user)
                <a href="{{ route('messages.show', $user->id) }}"
                   class="flex items-center px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer
                   {{ isset($selectedUser) && $selectedUser->id === $user->id ? 'bg-indigo-600 text-white' : 'text-gray-900 dark:text-gray-100' }}">

                   <!-- Indicador online/offline -->
                   <span class="inline-block w-3 h-3 rounded-full mr-2
                        {{ $user->isOnline() ? 'bg-green-500' : 'bg-gray-400' }}"
                        title="{{ $user->isOnline() ? 'Online' : 'Offline' }}">
                   </span>

                   {{ $user->name }}
                </a>
            @endforeach
        </div>

        <!-- Área principal da conversa -->
        <div class="flex flex-col w-2/3">
            @if(isset($selectedUser))
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Conversa com {{ $selectedUser->name }}</h2>
                </div>

                <div id="messages-container" class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50 dark:bg-gray-900">
                    @forelse ($messages as $msg)
                        @php
                            $isSender = $msg->sender_id === $currentUser->id;
                        @endphp
                        <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs px-4 py-2 rounded-lg
                                {{ $isSender ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                                <p class="whitespace-pre-wrap">{{ $msg->body }}</p>
                                <span class="block mt-1 text-xs text-gray-700 dark:text-gray-300 text-right">
                                    {{ $msg->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">Nenhuma mensagem nesta conversa.</p>
                    @endforelse
                </div>

                <form action="{{ route('messages.store') }}" method="POST" class="p-4 border-t border-gray-200 dark:border-gray-700 flex space-x-2">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">

                    <textarea name="body" rows="2" required
                        class="flex-grow rounded-md border border-gray-300 dark:border-gray-600 p-2 resize-none
                        bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500"
                        placeholder="Digite sua mensagem...">{{ old('body') }}</textarea>

                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition">
                        Enviar
                    </button>
                </form>

            @else
                <div class="flex items-center justify-center flex-grow text-gray-500 dark:text-gray-400">
                    Selecione um contato para começar a conversar.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
