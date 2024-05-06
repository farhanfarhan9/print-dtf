<div>
    <x-slot name="header">
        <a href="{{ route('customer.index') }}" wire:navigate
            class="flex items-center w-1/12 gap-1 p-2 mb-5 text-lg rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </x-slot>
    <div class="mt-10">
        <x-card shadow='false'>
            <form action="" method="post" wire:submit='save' class="space-y-7">
                <x-input label="Nama Customer" wire:model='name' placeholder="Nama Customer" />
                <div class="flex justify-between gap-10">

                <x-select
                    label="Provinsi"
                    wire:model.live="selectedProvinsi"
                    placeholder="Select Provinsi"
                    :async-data="route('api.provinsi.index')"
                    option-label="name"
                    option-value="id"
                    wire:change="updateCities($event.target.value)"
                />

                @if($selectedProvinsi)
                    <x-select
                        label="Kota/Kabupaten"
                        wire:model.live="selectedKota"
                        placeholder="Select Kota"
                        :async-data="route('api.kota.index', ['province' => $selectedProvinsi])"
                        option-label="name"
                        option-value="id"
                        wire:change="updateDistricts($event.target.value)"
                    />
                @endif

                @if($selectedKota)
                <x-select
                    label="Kecamatan"
                    wire:model.live="selectedKecamatan"
                    placeholder="Select Kecamatan"
                    :async-data="route('api.kecamatan.index', ['city' => $selectedKota])"
                    option-label="name"
                    option-value="id"
                    wire:change="updatePostal($event.target.value)"
                />
                @endif

                @if($selectedKecamatan)
                <x-select
                    label="Postal"
                    wire:model.live="selectedPostal"
                    placeholder="Select Kode Pos"
                    :async-data="route('api.pos.index', ['province' => $selectedProvinsi,'city' => $selectedKota])"
                    option-label="name"
                    option-value="name"
                />
                @endif

                    {{-- <x-input label="Kota/Kecamatan" wire:model='city' placeholder="Kota/Kecamatan" /> --}}
                    {{-- <x-input type="number" label="Kode Pos" wire:model='postal' placeholder="Kode Pos" /> --}}
                </div>
                <div class="flex justify-between gap-10">
                    <x-input type="number" label="No. Telp" wire:model='phone' placeholder="No. Telp" />
                    <x-inputs.currency type="number" label="Deposit" wire:model='deposit' placeholder="Deposit" />
                </div>
                <x-textarea label="Alamat" wire:model='address' placeholder="Alamat" />
                {{-- Checkbox for Is Reseller --}}
                <x-checkbox label="Reseller" wire:model="isReseller" />

                <x-button type="submit" spinner label="Simpan" green />
                <x-button href="{{ route('customer.index') }}" wire:navigate label="Batal" red />
            </form>
        </x-card>
    </div>
</div>
