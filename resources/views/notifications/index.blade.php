<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notificações
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filtros e botão "Marcar todas como lidas" --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-white p-4 rounded shadow-sm">
                <div class="space-x-4 mb-2 sm:mb-0">
                    <a href="{{ route('notifications', ['filter' => 'all']) }}"
                       class="text-sm font-medium {{ request('filter') !== 'unread' ? 'text-blue-600 underline' : 'text-gray-600 hover:underline' }}">
                        Todas
                    </a>
                    <a href="{{ route('notifications', ['filter' => 'unread']) }}"
                       class="text-sm font-medium {{ request('filter') === 'unread' ? 'text-blue-600 underline' : 'text-gray-600 hover:underline' }}">
                        Não lidas
                    </a>
                </div>

                @if($notifications->whereNull('read_at')->count())
                    <form method="POST" action="{{ route('notifications.read_all') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Marcar todas como lidas
                        </button>
                    </form>
                @endif
            </div>

            {{-- Lista de notificações --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($notifications->isEmpty())
                    <p class="text-gray-600">Você não tem notificações.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $type = $data['type'] ?? null;
                                $userName = $data['user_name'] ?? 'Alguém';
                            @endphp
                            <li class="py-4 px-4 flex flex-col sm:flex-row justify-between items-start sm:items-center {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} rounded-md mb-2 shadow-sm">
                                <div class="text-sm text-gray-700">
                                    @if($type === 'like')
                                        <span class="font-medium">{{ $userName }}</span> curtiu seu post.
                                    @elseif($type === 'comment')
                                        <span class="font-medium">{{ $userName }}</span> comentou no seu post.
                                    @elseif($type === 'follow')
                                        <span class="font-medium">{{ $userName }}</span> começou a te seguir.
                                    @else
                                        <span>Você tem uma nova notificação.</span>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>

                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:underline mt-2 sm:mt-0">
                                            Marcar como lida
                                        </button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
