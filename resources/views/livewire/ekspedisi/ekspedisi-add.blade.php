<div>
    <x-slot name="header">
        <a href="{{ route('ekspedisi-view') }}" wire:navigate
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
            <form wire:submit="save">
                <div class="flex space-x-4">
                    <!-- Nama Ekspedisi -->
                    <div class="flex-1">
                        <x-input label="Nama Ekspedisi" placeholder="Nama ekspedisi" wire:model="namaEkspedisi" />
                    </div>

                    <!-- Ongkir -->
                    <div class="flex-1">
                        <x-inputs.currency class="!pl-[2.5rem]" label="Ongkir" placeholder="Ongkir" prefix="Rp."
                            wire:model="ongkir" />
                    </div>
                </div>

                <!-- Save button -->
                <div class="mt-4">
                    <x-button type="submit" spinner label="Simpan" green />
                    <x-button href="{{ route('ekspedisi-view') }}" wire:navigate label="Batal" red />
                </div>
            </form>
        </x-card>
    </div>
</div>
