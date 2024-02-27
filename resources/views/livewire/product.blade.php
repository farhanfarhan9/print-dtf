<div>
    @if (session('productCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('productCreated')[0] }}',
                    description: '{{ session('productCreated')[1] }}',
                    icon: '{{ session('productCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('productEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('productEdited')[0] }}',
                    description: '{{ session('productEdited')[1] }}',
                    icon: '{{ session('productEdited')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @endif
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
            <x-button label="Tambah Data Produk" href="{{ route('product.add') }}" wire:navigate
                class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green icon="plus" />
        </div>
        {{-- <x-button label="+ Tambah Data Produk" primary wire:click='addData' /> --}}
        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Product Name</th>
                        <th scope="col" class="px-6 py-3">Stock</th>
                        <th scope="col" class="px-6 py-3">Price Details</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4">{{ $product->nama_produk }}</td>
                            <td class="px-6 py-4">{{ $product->stok }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $detailHarga = json_decode($product->detail_harga, true);
                                @endphp

                                @foreach ($detailHarga as $detail)
                                    Range: {{ $detail['start'] }} m - {{ $detail['end'] }} m, Harga:
                                    Rp.{{ $detail['price'] }}<br>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <x-button wire:click="editProduct({{ $product->id }})" label="Edit" primary />
                                    <x-button wire:click="deleteDialog({{ $product->id }})" label="Hapus" red />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
