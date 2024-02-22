<div>
    <form wire:submit.prevent="save">
        <x-input label="Nama Produk" placeholder="Nama produk" wire:model="productName" />

        <!-- Adding margin-top to the label -->
        <label for="title" class="mt-4 block">Range Harga (dalam meter)</label>

        @foreach($priceRanges as $index => $range)
            <div class="flex space-x-2 items-center mt-2">
                <!-- Adding margin-top to each row of price ranges -->
                <x-input type="number" placeholder="Start" wire:model="priceRanges.{{ $index }}.start" />
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
            <x-input label="STOK" wire:model="stock" />
        </div>

        <!-- Adding margin-top to the save button -->
        <div class="mt-4">
            <x-button label="Simpan" primary wire:click.prevent="save" />
        </div>
    </form>
</div>
