<div>
    <x-slot name="header">
        <a href="{{ route('products-view') }}" wire:navigate
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
            <form wire:submit.prevent="save">
                <x-input label="Nama Produk" placeholder="Nama produk" wire:model.defer="nama_produk" />

                <label for="title" class="block mt-4 text-sm font-medium">Range Harga (dalam meter)</label>

                @foreach ($priceRanges as $index => $range)
                    <div class="flex items-center mt-2 space-x-2">
                        <x-input type="number" placeholder="Start"
                            wire:model.defer="priceRanges.{{ $index }}.start" />
                        <span>—</span>
                        <x-input type="number" placeholder="End"
                            wire:model.defer="priceRanges.{{ $index }}.end" />
                        <x-inputs.currency placeholder="Harga" wire:model.defer="priceRanges.{{ $index }}.price" />
                        <x-button label="-" negative wire:click.prevent="removePriceRange({{ $index }})" />
                    </div>
                @endforeach

                <div class="my-4">
                    <x-button label="+" primary wire:click.prevent="addPriceRange" />
                </div>

                <label for="title" class="block mt-4 text-sm font-medium">Range Retail Harga (dalam meter)</label>

                @foreach ($priceRetailRanges as $index => $rangeRetail)
                    <div class="flex items-center mt-2 space-x-2">
                        <x-input type="number" placeholder="Start"
                            wire:model.defer="priceRetailRanges.{{ $index }}.start" />
                        <span>—</span>
                        <x-input type="number" placeholder="End"
                            wire:model.defer="priceRetailRanges.{{ $index }}.end" />
                        <x-inputs.currency placeholder="Harga" wire:model.defer="priceRetailRanges.{{ $index }}.price" />
                        <x-button label="-" negative wire:click.prevent="removePriceRetailRange({{ $index }})" />
                    </div>
                @endforeach

                <div class="my-4">
                    <x-button label="+" primary wire:click.prevent="addPriceRetailRange" />
                </div>

                <x-input label="Stok (dalam meter)" wire:model.defer="stok" />

                <div class="mt-4">
                    <x-button label="Simpan" primary type="submit" />
                </div>
            </form>
        </x-card>
    </div>

    @if (session()->has('message'))
        <div class="mt-4">
            {{ session('message') }}
        </div>
    @endif
</div>
