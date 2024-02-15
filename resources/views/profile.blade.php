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
                    <h2 class="text-lg font-medium text-gray-900 item-center">
                        Alamat
                    </h2>
                    <x-button class="item-center" wire:navigate href="{{route('address.index')}}" green label="Atur Alamat" />
                </header>
                <div class="px-5 py-2 mt-2 border rounded-lg space-y-7">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-lg font-bold">Kota/Kecamatan</p>
                            <p>{{ Auth::user()->address->city }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Kode Pos</p>
                            <p>{{ Auth::user()->address->postal }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Nomor Hp</p>
                            <p>{{ Auth::user()->address->phone }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Alamat</p>
                        <p>{{ Auth::user()->address->address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
