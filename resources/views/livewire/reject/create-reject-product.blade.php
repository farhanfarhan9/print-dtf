<div>
    <x-slot name="header">
        <a href="{{ route('rejected-products.index') }}" wire:navigate
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
                <x-native-select wire:model.live='product_id' label="Pilih produk">
                    @foreach ($products as $product)
                        <option hidden>Pilih produk</option>
                        <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                    @endforeach
                </x-native-select>
                <x-input shadowless="true" wire:model.live.debounce.300ms='qty' label="Stok" type="number"
                    step=".01" placeholder="Stok" />
                <x-button type="submit" spinner label="Simpan" green />
                <x-button href="{{ route('rejected-products.index') }}" wire:navigate label="Batal" red />
            </form>
        </x-card>
    </div>
</div>
