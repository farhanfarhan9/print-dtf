<div>
    <div class="my-4">
        <x-button label="+ Tambah Data Produk" primary wire:click='addData' />
    </div>

    @if (session()->has('message'))
        <x-alert title="{{ session('message') }}!" positive />
    @endif

    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
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
                @if($ekspedisi->first() == null)
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
                                <!-- Edit Button -->
                                <button type="button" class="p-2 text-sm text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200 focus:outline-none focus:ring" wire:click="editProduct({{ $product->id }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17h2v2h-2v-2z m0 0v-6h2v6m-2 0h-3.5a2 2 0 0 1-2-2v-3.5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3.5a2 2 0 0 1-2 2H11z"></path></svg>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="p-2 text-sm text-red-600 bg-red-100 rounded-full hover:bg-red-200 focus:outline-none focus:ring" x-on:click="$openModal('deleteModal')" wire:click="confirmDelete({{ $product->id }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m8-4H7a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-modal name="deleteModal">
        <x-card>
            <x-slot name="title">Delete Product</x-slot>
            <p>
                Are you sure you want to delete this product?
            </p>
            <x-slot name="footer">
                <x-button flat label="Cancel" wire:click="$set('confirmingProductDeletion', null)" x-on:click="close" />

                <x-button primary label="I Agree" wire:click="deleteProduct" x-on:click="close"/>
            </x-slot>
        </x-card>
    </x-modal>
</div>
