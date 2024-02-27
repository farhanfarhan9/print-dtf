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
            <form wire:submit="save">
                <x-input label="Nama Produk" placeholder="Nama produk" wire:model="productName" />

                <!-- Adding margin-top to the label -->
                <label for="title" class="block mt-4 text-sm font-medium">Range Harga (dalam meter)</label>

                @foreach ($priceRanges as $index => $range)
                    <div class="flex items-center mt-2 space-x-2">
                        <!-- Adding margin-top to each row of price ranges -->
                        <x-input type="number" placeholder="Start"
                            wire:model="priceRanges.{{ $index }}.start" />
                        <span>—</span>
                        <x-input type="number" placeholder="End" wire:model="priceRanges.{{ $index }}.end" />
                        <x-input placeholder="Harga" wire:model="priceRanges.{{ $index }}.price" />
                        <x-button label="-" negative wire:click.prevent="removePriceRange({{ $index }})" />
                    </div>
                @endforeach

                <!-- Adding margin-top to the button -->
                <div class="mt-4">
                    <x-button label="+" primary wire:click.prevent="addPriceRange" />
                </div>

                <!-- Adding margin-top to the input -->
                <div class="mt-4">
                    <x-input label="Stok (dalam meter)" type="number" placeholder="Jumlah stok" wire:model="stock" />
                </div>

                <!-- Adding margin-top to the save button -->
                <div class="mt-4">
                    <x-button type="submit" spinner label="Simpan" green />
                    <x-button href="{{ route('products-view') }}" wire:navigate label="Batal" red />
                </div>
            </form>
        </x-card>
    </div>
</div>
