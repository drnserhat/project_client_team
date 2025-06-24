<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-black-200 leading-tight" style="color:black">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Left Column: Forms --}}
                <div class="md:col-span-1 space-y-8">
                    {{-- Create Project Form --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Yeni Proje Oluştur</h3>
                            <form action="{{ route('projects.store') }}" method="POST">
                                @csrf
                                <div>
                                    <x-input-label style="color:white" for="project_name" :value="__('Proje Adı')" />
                                    <x-text-input id="project_name" class="block mt-1 w-full" type="text" name="project_name" required autofocus />
                                </div>
                                <x-primary-button class="mt-4">
                                    {{ __('Oluştur') }}
                                </x-primary-button>
                            </form>
                        </div>
                    </div>

                    {{-- Join Project Form --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white-100 mb-4" style="color:white">Bir Projeye Katıl</h3>
                            <form action="{{ route('projects.join') }}" method="POST">
                                @csrf
                                <div>
                                    <x-input-label style="color:white" for="unique_key" :value="__('Benzersiz Proje Kodu')" />
                                    <x-text-input id="unique_key" class="block mt-1 w-full" type="text" name="unique_key" placeholder="Kodu Buraya Girin" required />
                                </div>
                                <x-primary-button class="mt-4">
                                    {{ __('Katıl') }}
                                </x-primary-button>
                                @if(session('error'))
                                    <p class="mt-2 text-sm text-red-600">{{ session('error') }}</p>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Project List --}}
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Projelerim</h3>
                            <div class="space-y-4">
                                @forelse ($projects as $project)
                                    <div class="border dark:border-gray-700 rounded-lg p-4 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $project->project_name }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Benzersiz Kod: <span class="font-mono bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">{{ $project->unique_key }}</span></p>
                                        </div>
                                        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                            Projeye Git →
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">Henüz bir projeye dahil değilsiniz.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
