<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <header class="flex justify-between">
                    <h2 class="text-lg font-medium text-gray-900">
                        Alamat
                    </h2>
                    <x-button wire:navigate href="{{route('address.index')}}" green label="Atur Alamat" />
                </header>
            </div>
        </div>
    </div>
</x-app-layout>
