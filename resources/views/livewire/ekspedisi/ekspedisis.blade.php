<div>
    @if (session('expeditionCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('expeditionCreated')[0] }}',
                    description: '{{ session('expeditionCreated')[1] }}',
                    icon: '{{ session('expeditionCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('expeditionEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('expeditionEdited')[0] }}',
                    description: '{{ session('expeditionEdited')[1] }}',
                    icon: '{{ session('expeditionEdited')[2] }}',
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
                placeholder="Cari Ekspedisi" />
            <x-button label="Tambah Data Ekspedisi" href="{{ route('ekspedisi.add') }}"
                class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green icon="plus" />
        </div>

        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Ekspedisi</th>
                        <th scope="col" class="px-6 py-3">Ongkir</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ekspedisi as $key => $ekspedisi)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4">{{ $key + 1 }}</td>
                            <td class="px-6 py-4">{{ $ekspedisi->nama_ekspedisi }}</td>
                            <td class="px-6 py-4">{{ rupiah_format($ekspedisi->ongkir) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex gap-5">
                                        <x-button wire:click="editEkspedisi({{ $ekspedisi->id }})" label="Edit"
                                            primary />
                                        <x-button wire:click="deleteDialog({{ $ekspedisi->id }})" label="Hapus" red />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
