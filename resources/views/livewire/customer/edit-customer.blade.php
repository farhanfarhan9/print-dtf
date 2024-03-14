<div>
    <x-slot name="header">
        <a href="{{ route('customer.index') }}" wire:navigate class="flex items-center w-1/12 gap-1 p-2 mb-5 text-lg rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </x-slot>
    <div class="mt-10">
        <x-card shadow='false'>
            <form wire:submit.prevent='save' class="space-y-7">
                <x-input label="Nama Customer" wire:model='name' placeholder="Nama Customer" />

                @if($change == false)
                    <x-button wire:click=changeLocation spinner label="Rubah Lokasi" green />
                @else
                    <x-button wire:click=cancelChangeLocation spinner label="Batal Rubah Lokasi" red />
                @endif
                <div class="flex justify-between gap-10">
                    @if($change == false)
                        <x-input label="Provinsi" wire:model='selectedDataNameProvinsi' placeholder="Provinsi" readonly/>
                        <x-input label="Kota" wire:model='selectedDataNameKota' placeholder="Kota" readonly/>
                        <x-input label="kecamatan" wire:model='selectedDataNameKecamatan' placeholder="kecamatan" readonly/>
                        <x-input label="Kode Pos" wire:model='selectedDataPostal' placeholder="Kode Pos" readonly/>
                    @endif

                    @if($change == true)
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
                                label="Kota"
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

                        @if($selectedProvinsi && $selectedKota && $selectedKecamatan)
                        <x-select
                            label="Postal"
                            wire:model.live="selectedPostal"
                            placeholder="Select Kode Pos"
                            :async-data="route('api.pos.index', ['province' => $selectedProvinsi,'city' => $selectedKota])"
                            option-label="name"
                            option-value="id"
                        />
                        @endif
                    @endif
                </div>
                <div class="flex justify-between gap-10">
                    <x-input type="number" label="No. Telp" wire:model='phone' placeholder="No. Telp" />
                    <div class="w-full">
                        <x-input type="number" label="Tambah Deposit" wire:model='newDeposit' placeholder="Tambah Deposit" />
                        <p>Deposit saat ini {{rupiah_format($deposit)}}</p>
                    </div>
                </div>
                <x-textarea label="Alamat" wire:model='address' placeholder="Alamat" />

                <x-button type="submit" spinner label="Simpan" green />
                <x-button href="{{ route('customer.index') }}" wire:navigate label="Batal" red />
                @if($change == false)
                    <x-input type="hidden" wire:model='selectedDataNameProvinsi'/>
                    <x-input type="hidden" wire:model='selectedDataNameKota'/>
                    <x-input type="hidden" wire:model='selectedDataNameKecamatan'/>
                @endif
            </form>
        </x-card>
    </div>
</div>
