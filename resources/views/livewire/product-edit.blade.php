<div>
    <form wire:submit.prevent="save">
        <x-input label="Nama Produk" placeholder="Nama produk" wire:model.defer="nama_produk" />

        <label for="title" class="mt-4 block">Range Harga (dalam meter)</label>

        @foreach($priceRanges as $index => $range)
            <div class="flex space-x-2 items-center mt-2">
                <x-input type="number" placeholder="Start" wire:model.defer="priceRanges.{{ $index }}.start" />
                <span>—</span>
                <x-input type="number" placeholder="End" wire:model.defer="priceRanges.{{ $index }}.end" />
                <x-input placeholder="Harga" wire:model.defer="priceRanges.{{ $index }}.price" />
                <x-button label="-" negative wire:click.prevent="removePriceRange({{ $index }})" />
            </div>
        @endforeach

        <div class="my-4">
            <x-button label="+" primary wire:click.prevent="addPriceRange" />
        </div>

        <x-input label="STOK" wire:model.defer="stok" />

        <div class="mt-4">
            <x-button label="Simpan" primary type="submit" />
        </div>
    </form>

    @if(session()->has('message'))
        <div class="mt-4">
            {{ session('message') }}
        </div>
    @endif
</div>
