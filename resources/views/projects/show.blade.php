<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-black-200 leading-tight">
            {{ $project->project_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Chat Area --}}
                    <div id="message-container" class="h-96 overflow-y-auto mb-4 p-4 border rounded-lg dark:border-gray-700 flex flex-col-reverse">
                        @php
                            $items = $messages->concat($project->fileShares)->sortBy('created_at');
                        @endphp

                        @forelse($items->reverse() as $item)
                            @if($item instanceof \App\Models\Message)
                                {{-- Message Template --}}
                                <div class="flex items-start gap-2.5 my-2 {{ $item->user_id == auth()->id() ? 'justify-end' : '' }}">
                                    <img class="w-8 h-8 rounded-full object-cover" src="{{ $item->user->image_url ? asset('storage/' . $item->user->image_url) : 'https://ui-avatars.com/api/?name='.urlencode($item->user->name) }}" alt="{{$item->user->name}}">
                                    <div class="flex flex-col gap-1 max-w-[320px] p-4 border-gray-200 rounded-lg dark:bg-gray-700 {{ $item->user_id == auth()->id() ? 'rounded-se-none bg-blue-600 text-white' : 'rounded-ss-none bg-gray-600' }}">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                            <span class="text-sm font-semibold">{{ $item->user->name }}</span>
                                            <span class="text-xs font-normal text-gray-400">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm font-normal py-2.5">{{ $item->content }}</p>
                                    </div>
                                </div>
                            @elseif($item instanceof \App\Models\FileShare)
                                {{-- FileShare Template --}}
                                <div class="flex items-start gap-2.5 my-2 {{ $item->user_id == auth()->id() ? 'justify-end' : '' }}">
                                    <img class="w-8 h-8 rounded-full object-cover" src="{{ $item->user->image_url ? asset('storage/' . $item->user->image_url) : 'https://ui-avatars.com/api/?name='.urlencode($item->user->name) }}" alt="{{$item->user->name}}">
                                    <div class="flex flex-col gap-1 max-w-[320px] p-4 border-gray-200 rounded-lg dark:bg-gray-700 {{ $item->user_id == auth()->id() ? 'rounded-se-none bg-green-600 text-white' : 'rounded-ss-none bg-gray-600' }}">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                            <span class="text-sm font-semibold">{{ $item->user->name }}</span>
                                            <span class="text-xs font-normal text-gray-400">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex items-center p-2 rounded-b-lg">
                                            <svg class="w-8 h-8 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 20"><path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M6 1v4a1 1 0 0 1-1 1H1m14-4v16a.97.97 0 0 1-.933 1H1.933A.97.97 0 0 1 1 18V5.828a2 2 0 0 1 .586-1.414l2.828-2.828A2 2 0 0 1 5.828 1h8.239A.97.97 0 0 1 15 2Z"/></svg>
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-sm font-normal underline hover:no-underline ml-2">{{ $item->file_name }}</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div id="empty-message" class="text-center text-gray-500">
                                Henüz hiç mesaj veya dosya yok.
                            </div>
                        @endforelse
                    </div>

                    {{-- Message and File Input Form --}}
                    <form id="message-form" class="flex items-center gap-4" onsubmit="return false;">
                        <input type="text" id="message-input" class="flex-grow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Mesajınızı yazın...">
                        
                        <input type="file" id="file-input" class="hidden">
                        <button type="button" id="file-button" class="inline-flex items-center justify-center rounded-lg p-2.5 text-sm font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </button>

                        <button type="submit" id="send-button" class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Gönder
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const projectId = '{{ $project->id }}';
            const authUserId = {{ auth()->id() }};
            let latestTimestamp = '{{ $items->first()->created_at ?? now() }}';

            const messageContainer = document.getElementById('message-container');
            const messageInput = document.getElementById('message-input');
            const fileButton = document.getElementById('file-button');
            const fileInput = document.getElementById('file-input');
            const sendButton = document.getElementById('send-button');
            const emptyMessage = document.getElementById('empty-message');

            function scrollToBottom() {
                messageContainer.scrollTop = 0; // flex-col-reverse
            }
            scrollToBottom();
            
            function hideEmptyMessage() {
                if(emptyMessage) {
                    emptyMessage.style.display = 'none';
                }
            }
            
            // Handle Message Form Submission
            function sendMessage() {
                if(messageInput.value.trim() === '') return;
                axios.post(`{{ route('messages.store', $project) }}`, { content: messageInput.value })
                    .then(response => messageInput.value = '')
                    .catch(error => console.error('Mesaj gönderilemedi:', error));
            }
            sendButton.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', e => e.key === 'Enter' && (e.preventDefault(), sendMessage()));

            // Handle File Upload
            fileButton.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                const formData = new FormData();
                formData.append('file', file);
                axios.post(`{{ route('files.store', $project) }}`, formData)
                    .catch(error => console.error('Dosya yüklenemedi:', error.response.data));
                this.value = '';
            });

            // AJAX Polling for new items
            setInterval(() => {
                axios.get(`{{ route('projects.updates', $project) }}`, {
                    params: { last_timestamp: latestTimestamp }
                })
                .then(response => {
                    if (response.data.length > 0) {
                        hideEmptyMessage();
                        response.data.forEach(item => {
                            let itemHtml;
                            if (item.content) { // It's a message
                                itemHtml = createMessageHtml(item.user, item.content, 'şimdi', item.user.id);
                            } else { // It's a file
                                itemHtml = createFileHtml(item.user, item.file_name, 'şimdi', '{{ asset('storage') }}/' + item.file_path, item.user.id);
                            }
                            messageContainer.insertAdjacentHTML('afterbegin', itemHtml);
                        });
                        latestTimestamp = response.data[response.data.length - 1].created_at;
                        scrollToBottom();
                    }
                })
                .catch(error => console.error('Yeni veriler alınamadı:', error));
            }, 3000); // Poll every 3 seconds

            // HTML Generator Functions (These remain unchanged)
            function createMessageHtml(user, content, time, userId) {
                const isAuthUser = userId === authUserId;
                const justifyClass = isAuthUser ? 'justify-end' : '';
                const bubbleClass = isAuthUser ? 'rounded-se-none bg-blue-600 text-white' : 'rounded-ss-none bg-gray-600';
                const avatarUrl = user.image_url ? `{{ asset('storage') }}/${user.image_url}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`;
                return `
                    <div class="flex items-start gap-2.5 my-2 ${justifyClass}">
                        <img class="w-8 h-8 rounded-full object-cover" src="${avatarUrl}" alt="${user.name}">
                        <div class="flex flex-col gap-1 max-w-[320px] p-4 border-gray-200 rounded-lg dark:bg-gray-700 ${bubbleClass}">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <span class="text-sm font-semibold">${user.name}</span>
                                <span class="text-xs font-normal text-gray-400">${time}</span>
                            </div>
                            <p class="text-sm font-normal py-2.5">${content}</p>
                        </div>
                    </div>`;
            }

            function createFileHtml(user, fileName, time, filePath, userId) {
                const isAuthUser = userId === authUserId;
                const justifyClass = isAuthUser ? 'justify-end' : '';
                const bubbleClass = isAuthUser ? 'rounded-se-none bg-green-600 text-white' : 'rounded-ss-none bg-gray-600';
                const avatarUrl = user.image_url ? `{{ asset('storage') }}/${user.image_url}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`;

                return `
                    <div class="flex items-start gap-2.5 my-2 ${justifyClass}">
                        <img class="w-8 h-8 rounded-full object-cover" src="${avatarUrl}" alt="${user.name}">
                        <div class="flex flex-col gap-1 max-w-[320px] p-4 border-gray-200 rounded-lg dark:bg-gray-700 ${bubbleClass}">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <span class="text-sm font-semibold">${user.name}</span>
                                <span class="text-xs font-normal text-gray-400">${time}</span>
                            </div>
                            <div class="flex items-center p-2 rounded-b-lg">
                                <svg class="w-8 h-8 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 20"><path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M6 1v4a1 1 0 0 1-1 1H1m14-4v16a.97.97 0 0 1-.933 1H1.933A.97.97 0 0 1 1 18V5.828a2 2 0 0 1 .586-1.414l2.828-2.828A2 2 0 0 1 5.828 1h8.239A.97.97 0 0 1 15 2Z"/></svg>
                                <a href="${filePath}" target="_blank" class="text-sm font-normal underline hover:no-underline ml-2">${fileName}</a>
                            </div>
                        </div>
                    </div>`;
            }
        });
    </script>
    @endpush
</x-app-layout> 