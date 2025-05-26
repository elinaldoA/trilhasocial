<x-app-layout>
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Trilha</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('trails.update', $trail) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $trail->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <!-- Localização -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Localização</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $trail->location) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                </div>
            </div>

            <!-- Difficulty -->
            <div>
                <label for="difficulty" class="block text-sm font-medium text-gray-700">Dificuldade</label>
                <select name="difficulty" id="difficulty" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @php
                        $difficulties = ['Fácil', 'Médio', 'Difícil'];
                        $selectedDifficulty = old('difficulty', $trail->difficulty);
                    @endphp
                    @foreach ($difficulties as $level)
                        <option value="{{ $level }}" {{ $selectedDifficulty === $level ? 'selected' : '' }}>
                            {{ $level }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Distance -->
            <div>
                <label for="distance" class="block text-sm font-medium text-gray-700">Distância (km)</label>
                <input type="number" name="distance" id="distance" value="{{ old('distance', $trail->distance) }}"
                    min="0" step="0.01" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <!-- Avg Time -->
            <div>
                <label for="avg_time" class="block text-sm font-medium text-gray-700">Tempo Médio (minutos)</label>
                <input type="number" name="avg_time" id="avg_time" value="{{ old('avg_time', $trail->avg_time) }}"
                    min="1" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <!-- Descrição -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="description" id="description" rows="5" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $trail->description) }}</textarea>
            </div>

            <!-- Upload de novas imagens -->
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Adicionar Novas
                    Imagens</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-600 file:text-white
                        hover:file:bg-blue-700
                        cursor-pointer" />
            </div>

            <!-- Imagens atuais -->
            @if ($trail->images->count())
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-3">Imagens atuais:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($trail->images as $image)
                            <div class="relative group rounded overflow-hidden shadow" id="image-{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem"
                                    class="w-full h-32 object-cover transition-opacity duration-200">

                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}"
                                    class="hidden" id="remove-image-{{ $image->id }}">

                                <div class="absolute top-1 right-1">
                                    <button type="button" onclick="toggleImageRemoval({{ $image->id }})"
                                        class="bg-red-600 text-white text-xs px-2 py-1 rounded">
                                        Remover
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Ações -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('trails.index') }}"
                    class="inline-block px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-semibold transition">
                    Salvar
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleImageRemoval(imageId) {
            const checkbox = document.getElementById('remove-image-' + imageId);
            const imageContainer = document.getElementById('image-' + imageId);

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                imageContainer.classList.add('opacity-50', 'ring-2', 'ring-red-500');
            } else {
                imageContainer.classList.remove('opacity-50', 'ring-2', 'ring-red-500');
            }
        }
    </script>
</x-app-layout>
