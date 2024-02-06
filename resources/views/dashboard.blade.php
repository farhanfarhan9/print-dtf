<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                {{ __('Dashboard') }}
            </h2>
            {{-- <button class="w-10 h-10 bg-green-400">wdw</button> --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-4xl">Hi There</h1>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
