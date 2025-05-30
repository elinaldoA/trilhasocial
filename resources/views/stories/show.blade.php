<x-app-layout>
    <div x-data="storyViewer()" class="fixed inset-0 bg-black z-50">
        <!-- Barra de progresso melhorada -->
        <div class="absolute top-0 left-0 right-0 h-1 flex gap-0.5 p-1">
            <template x-for="(story, index) in stories" :key="story.id">
                <div class="h-full flex-1 bg-gray-600 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-white transition-all duration-75 ease-linear"
                        :class="{
                            'w-full': currentStoryIndex > index,
                            'w-0': currentStoryIndex < index,
                            'bg-white/50': currentStoryIndex > index
                        }"
                        :style="{ width: currentStoryIndex === index ? progress + '%' : '0%' }"
                    ></div>
                </div>
            </template>
        </div>

        <!-- Story atual -->
        <template x-if="stories.length > 0">
            <div class="relative w-full h-full">
                <!-- Conteúdo do story -->
                <template x-if="currentStory.media_type === 'image'">
                    <img
                        :src="'/storage/' + currentStory.media_path"
                        class="w-full h-full object-contain"
                        @click="handleTap"
                        @touchstart="pauseProgress"
                        @touchend="resumeProgress"
                        @mousedown="pauseProgress"
                        @mouseup="resumeProgress"
                        @mouseleave="resumeProgress"
                    >
                </template>

                <template x-if="currentStory.media_type === 'video'">
                    <video
                        :src="'/storage/' + currentStory.media_path"
                        class="w-full h-full object-contain"
                        autoplay
                        @click="handleTap"
                        @ended="nextStory"
                        @touchstart="pauseProgress"
                        @touchend="resumeProgress"
                        @mousedown="pauseProgress"
                        @mouseup="resumeProgress"
                        @mouseleave="resumeProgress"
                        x-ref="video"
                    ></video>
                </template>

                <!-- Informações do usuário -->
                <div class="absolute top-4 left-4 flex items-center space-x-2">
                    <img
                        :src="user.profile_photo_url"
                        class="w-8 h-8 rounded-full border-2 border-white"
                    >
                    <span class="text-white font-semibold" x-text="user.username"></span>
                    <span class="text-white/80 text-sm" x-text="timeAgo"></span>
                </div>

                <!-- Controles invisíveis -->
                <div class="absolute inset-0 flex">
                    <div class="w-1/2 h-full" @click="prevStory"></div>
                    <div class="w-1/2 h-full" @click="nextStory"></div>
                </div>

                <!-- Fechar -->
                <button
                    @click="close"
                    class="absolute top-4 right-4 text-white text-2xl"
                >
                    &times;
                </button>

                <!-- Visualizações -->
                <button
                    @click="showViewers = true"
                    class="absolute bottom-4 left-4 text-white flex items-center space-x-1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span x-text="currentStory.viewers_count"></span>
                </button>

                <!-- Botão de like -->
                <button @click="likeStory" class="absolute bottom-16 right-4 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="{ 'fill-red-500': currentStory.is_liked }" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>

                <!-- Botão de resposta -->
                <button @click="showReplyInput = !showReplyInput" class="absolute bottom-4 right-4 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </button>

                <!-- Input de resposta -->
                <div x-show="showReplyInput" class="absolute bottom-20 left-0 right-0 p-4">
                    <div class="relative">
                        <input
                            x-model="replyMessage"
                            type="text"
                            class="w-full bg-white/20 text-white rounded-full py-3 px-4 pr-12 focus:outline-none"
                            placeholder="Envie uma mensagem..."
                            @keyup.enter="sendReply"
                        >
                        <button
                            @click="sendReply"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal de Visualizações -->
        <div x-show="showViewers" x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.away="showViewers = false">
            <div class="bg-white rounded-lg w-full max-w-md max-h-[80vh] overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Visualizações</h3>
                    <button @click="showViewers = false" class="text-gray-500 hover:text-gray-700">
                        &times;
                    </button>
                </div>
                <div class="overflow-y-auto">
                    <template x-for="viewer in currentStory.viewers" :key="viewer.id">
                        <div class="flex items-center justify-between p-4 border-b">
                            <div class="flex items-center space-x-3">
                                <img :src="viewer.profile_photo_path" class="w-10 h-10 rounded-full">
                                <span x-text="viewer.name" class="font-medium"></span>
                            </div>
                            <span x-text="formatDate(viewer.pivot.created_at)" class="text-sm text-gray-500"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
    function storyViewer() {
        return {
            stories: @json($stories),
            user: @json($user),
            currentStoryIndex: 0,
            progress: 0,
            interval: null,
            timeAgo: '',
            showViewers: false,
            showReplyInput: false,
            replyMessage: '',
            isPaused: false,
            startTime: null,
            remainingTime: 5000, // Tempo padrão de 5s para imagens

            get currentStory() {
                return this.stories[this.currentStoryIndex];
            },

            init() {
                this.updateTimeAgo();
                this.remainingTime = 5000; // Reset para o valor padrão

                if (!this.currentStory.viewers.some(v => v.id === {{ auth()->id() }})) {
                    this.markAsViewed();
                }

                this.startProgress();
            },

            startProgress() {
                clearInterval(this.interval);
                this.progress = 0;
                this.startTime = Date.now();
                this.isPaused = false;

                // Para vídeos, usamos a duração do vídeo
                if (this.currentStory.media_type === 'video') {
                    if (this.$refs.video) {
                        this.remainingTime = this.$refs.video.duration * 1000;
                    }
                }

                const intervalTime = 50;
                const totalTime = this.remainingTime;
                const increment = (intervalTime / totalTime) * 100;

                this.interval = setInterval(() => {
                    if (!this.isPaused) {
                        const elapsed = Date.now() - this.startTime;
                        this.progress = (elapsed / totalTime) * 100;

                        if (this.progress >= 100) {
                            this.nextStory();
                        }
                    }
                }, intervalTime);
            },

            pauseProgress() {
                if (!this.isPaused) {
                    this.isPaused = true;
                    clearInterval(this.interval);
                    // Calcula o tempo restante quando pausado
                    const elapsed = Date.now() - this.startTime;
                    this.remainingTime = this.remainingTime - elapsed;
                }
            },

            resumeProgress() {
                if (this.isPaused) {
                    this.isPaused = false;
                    this.startProgress();
                }
            },

            markAsViewed() {
                fetch(`/stories/${this.currentStory.id}/view`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    this.currentStory.viewers = data.viewers;
                    this.currentStory.viewers_count = data.viewers.length;
                });
            },

            likeStory() {
                fetch(`/stories/${this.currentStory.id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    this.currentStory.is_liked = data.is_liked;
                    this.currentStory.likes_count = data.likes_count;
                });
            },

            sendReply() {
                if (!this.replyMessage.trim()) return;

                fetch(`/stories/${this.currentStory.id}/reply`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: this.replyMessage
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.replyMessage = '';
                    this.showReplyInput = false;
                });
            },

            nextStory() {
                if (this.currentStoryIndex < this.stories.length - 1) {
                    this.currentStoryIndex++;
                    this.init();
                } else {
                    this.close();
                }
            },

            prevStory() {
                if (this.currentStoryIndex > 0) {
                    this.currentStoryIndex--;
                    this.init();
                }
            },

            handleTap(event) {
                const x = event.clientX;
                const width = window.innerWidth;

                if (x > width / 2) {
                    this.nextStory();
                } else {
                    this.prevStory();
                }
            },

            updateTimeAgo() {
                const date = new Date(this.currentStory.created_at);
                this.timeAgo = this.formatTimeAgo(date);
            },

            formatTimeAgo(date) {
                const seconds = Math.floor((new Date() - date) / 1000);
                let interval = Math.floor(seconds / 31536000);

                if (interval >= 1) return interval + " ano" + (interval === 1 ? "" : "s");
                interval = Math.floor(seconds / 2592000);
                if (interval >= 1) return interval + " mês" + (interval === 1 ? "" : "es");
                interval = Math.floor(seconds / 86400);
                if (interval >= 1) return interval + " dia" + (interval === 1 ? "" : "s");
                interval = Math.floor(seconds / 3600);
                if (interval >= 1) return interval + " hora" + (interval === 1 ? "" : "s");
                interval = Math.floor(seconds / 60);
                if (interval >= 1) return interval + " minuto" + (interval === 1 ? "" : "s");
                return Math.floor(seconds) + " segundo" + (seconds === 1 ? "" : "s");
            },

            formatDate(dateString) {
                const options = { hour: '2-digit', minute: '2-digit' };
                return new Date(dateString).toLocaleTimeString('pt-BR', options);
            },

            close() {
                window.location.href = '/';
            }
        }
    }
    </script>
</x-app-layout>
