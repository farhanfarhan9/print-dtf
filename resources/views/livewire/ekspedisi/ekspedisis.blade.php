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
                placeholder="Cari Ekspedisi" />
            <x-button label="Tambah Data Ekspedisi" href="{{ route('ekspedisi.add') }}" wire:navigate class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green
                icon="plus" />
        </div>

        <div class="overflow-x-auto mt-5 relative shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 px-6">Nama Ekspedisi</th>
                        <th scope="col" class="py-3 px-6">Ongkir</th>
                        <th scope="col" class="py-3 px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($ekspedisi->first() == null)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6 text-center" colspan="3">Data Kosong</td>
                    </tr>
                    @endif
                    @foreach($ekspedisi as $ekspedisi)
                        <tr class="bg-white border-b">
                            <td class="py-4 px-6">{{ $ekspedisi->nama_ekspedisi }}</td>
                            <td class="py-4 px-6">{{ $ekspedisi->ongkir }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex gap-5">
                                        <x-button wire:click="editEkspedisi({{ $ekspedisi->id }})" label="Edit"
                                            primary />
                                        <x-button wire:click="delete({{ $ekspedisi->id }})"
                                            wire:confirm="Apakah Anda yakin menghapus data ini? ?" label="Hapus" red />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
