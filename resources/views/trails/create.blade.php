<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">Nova Trilha</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-6">
        <form action="{{ route('trails.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" name="name" id="name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="description" id="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Localização</label>
                <input type="text" name="location" id="location" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="difficulty" class="block text-sm font-medium text-gray-700">Dificuldade</label>
                <select name="difficulty" id="difficulty" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="Fácil">Fácil</option>
                    <option value="Médio">Médio</option>
                    <option value="Difícil">Difícil</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="distance" class="block text-sm font-medium text-gray-700">Distância (km)</label>
                    <input type="number" step="0.1" name="distance" id="distance" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div>
                    <label for="avg_time" class="block text-sm font-medium text-gray-700">Tempo Médio (min)</label>
                    <input type="number" name="avg_time" id="avg_time" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                </div>
            </div>

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Imagens da trilha</label>
                <input type="file" name="images[]" id="images" multiple
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-600 file:text-white
                        hover:file:bg-blue-700
                        cursor-pointer
                    " />
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition">
                    Salvar Trilha
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
