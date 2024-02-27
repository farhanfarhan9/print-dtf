<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Produk
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex sm:justify-between">
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari Produk" />
            <x-button label="Tambah Data Produk" href="{{ route('product.add') }}" wire:navigate class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green
                icon="plus" />
        </div>
        {{-- <x-button label="+ Tambah Data Produk" primary wire:click='addData' /> --}}
        <div class="overflow-x-auto mt-5 relative shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 px-6">Product Name</th>
                        <th scope="col" class="py-3 px-6">Stock</th>
                        <th scope="col" class="py-3 px-6">Price Details</th>
                        <th scope="col" class="py-3 px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($products->first() == null)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6 text-center" colspan="3">Data Kosong</td>
                    </tr>
                    @endif
                    @foreach($products as $product)
                        <tr class="bg-white border-b">
                            <td class="py-4 px-6">{{ $product->nama_produk }}</td>
                            <td class="py-4 px-6">{{ $product->stok }}</td>
                            <td class="py-4 px-6">
                            @php
                            $detailHarga = json_decode($product->detail_harga, true);
                            @endphp

                            @foreach($detailHarga as $detail)
                                Range: {{ $detail['start'] }} m - {{ $detail['end'] }} m, Harga: Rp.{{ $detail['price'] }}<br>
                            @endforeach
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-4">
                                    <x-button wire:click="editProduct({{ $product->id }})" label="Edit"
                                        primary />
                                    <x-button wire:click="delete({{ $product->id }})"
                                        wire:confirm="Apakah Anda yakin menghapus data ini? ?" label="Hapus" red />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

