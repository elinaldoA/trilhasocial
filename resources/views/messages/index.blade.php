<x-app-layout>
    <x-slot name="header">
        Mensagens
    </x-slot>

    <div
        class="flex flex-col md:flex-row h-[600px] bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden border border-gray-300 dark:border-gray-700">

        <!-- Lista de usuários -->
        <div
            class="w-full md:w-80 border-b md:border-b-0 md:border-r border-gray-300 dark:border-gray-700 flex flex-col">
            <div
                class="p-4 border-b border-gray-300 dark:border-gray-700 font-semibold text-lg text-gray-900 dark:text-gray-100">
                Contatos
            </div>

            <div class="flex-grow overflow-y-auto">
                @foreach ($users as $user)
                    <a href="{{ route('messages.show', $user->id) }}"
                        class="flex items-center px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition
                            {{ isset($selectedUser) && $selectedUser->id === $user->id ? 'bg-indigo-600 text-white' : 'text-gray-900 dark:text-gray-100' }}">

                        <img src="{{ $user->avatar_url ?? asset('default-avatar.png') }}"
                            alt="Avatar de {{ $user->name }}"
                            class="w-10 h-10 rounded-full object-cover mr-3 border-2
                                {{ isset($selectedUser) && $selectedUser->id === $user->id ? 'border-white' : 'border-transparent' }}">

                        <div class="flex-1 truncate">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold truncate">{{ $user->name }}</span>
                                <span
                                    class="inline-block w-3 h-3 rounded-full
                                    {{ $user->isOnline() ? 'bg-green-500' : 'bg-gray-400' }}"
                                    title="{{ $user->isOnline() ? 'Online' : 'Offline' }}">
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Área principal da conversa -->
        <div class="flex flex-col flex-grow bg-gray-50 dark:bg-gray-900">

            @if (isset($selectedUser))
                <div class="flex items-center p-4 border-b border-gray-300 dark:border-gray-700">
                    <img src="{{ $selectedUser->avatar_url ?? asset('default-avatar.png') }}"
                        alt="Avatar de {{ $selectedUser->name }}"
                        class="w-12 h-12 rounded-full object-cover mr-4 border-2 border-indigo-600">

                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $selectedUser->name }}
                    </h2>
                </div>

                <div id="messages-container" class="flex-1 p-4 overflow-y-auto space-y-3 scroll-smooth">
                    @forelse ($messages as $msg)
                        @php
                            $isSender = $msg->sender_id === $currentUser->id;
                        @endphp

                        <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-xs px-4 py-2 rounded-lg
                                {{ $isSender
                                    ? 'bg-indigo-600 text-white rounded-br-none'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none' }}">
                                    {{ $msg->body }}
                                @if ($msg->attachment)
                                    <div class="mt-2">
                                        @php
                                            $ext = pathinfo($msg->attachment, PATHINFO_EXTENSION);
                                            $url = asset('storage/' . $msg->attachment);
                                        @endphp

                                        @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                            <a href="{{ $url }}" target="_blank" class="block">
                                                <img src="{{ $url }}" alt="Anexo imagem"
                                                    class="max-w-full rounded-md border border-gray-300">
                                            </a>
                                        @else
                                            <a href="{{ $url }}" target="_blank"
                                                class="underline text-blue-600 hover:text-blue-700">
                                                Ver anexo ({{ strtoupper($ext) }})
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <div class="text-xs mt-1 text-gray-400 dark:text-gray-500 text-right select-none">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>

                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 mt-20">Nenhuma mensagem nesta conversa.
                        </p>
                    @endforelse
                </div>

                <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-4 border-t border-gray-300 dark:border-gray-700 flex items-center space-x-3 bg-white dark:bg-gray-800">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">

                    <textarea name="body" rows="1" required
                        class="flex-grow resize-none rounded-full border border-gray-300 dark:border-gray-600 px-4 py-2
                               bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500
                               focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Mensagem..."></textarea>

                    <!-- Botão anexar arquivo -->
                    <label for="file-upload" class="cursor-pointer text-gray-500 hover:text-indigo-600 transition"
                        title="Anexar arquivo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.656-5.656l-6.586 6.586a6 6 0 108.485 8.485L21 13" />
                        </svg>
                    </label>
                    <input id="file-upload" name="attachment" type="file" class="hidden">

                    <button type="submit"
                        class="flex items-center justify-center w-12 h-12 bg-indigo-600 hover:bg-indigo-700 rounded-full text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-6 h-6 rotate-90">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-6.518-3.76a.75.75 0 00-1.06.67v7.784a.75.75 0 001.06.67l6.518-3.76a.75.75 0 000-1.34z" />
                        </svg>
                    </button>
                </form>
            @else
                <div
                    class="flex items-center justify-center flex-grow text-gray-500 dark:text-gray-400 p-4 text-center">
                    Selecione um contato para começar a conversar.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
