<div>
    @if (session('dataCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('dataCreated')[0] }}',
                    description: '{{ session('dataCreated')[1] }}',
                    icon: '{{ session('dataCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('customerEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('customerEdited')[0] }}',
                    description: '{{ session('customerEdited')[1] }}',
                    icon: '{{ session('customerEdited')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @endif
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Reject Product
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 sm:space-x-4">
            <x-input wire:model.live.debounce.300ms="search" icon="search" placeholder="Search product name..." class="w-full sm:w-1/4" shadowless="true" />
            <div class="flex space-x-4">
                <x-input wire:model.live="startDate" type="date" placeholder="Tanggal mulai" class="w-full" />
                <x-input wire:model.live="endDate" type="date" placeholder="Tanggal akhir" class="w-full" />
            </div>
            <div class="flex space-x-2">
                <x-button wire:click="exportExcel" label="Export" blue icon="download" class="w-full sm:w-auto" wire:loading.attr="disabled" wire:target="exportExcel">
                    <span wire:loading wire:target="exportExcel">Exporting...</span>
                </x-button>
                <x-button href="{{ route('rejected-products.create') }}" label="Tambah Data" green icon="plus" class="w-full sm:w-auto" />
            </div>
        </div>

        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama Produk
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Stok
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tanggal
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rejecteds as $key => $rejected)
                        <tr wire:key="{{ $rejected->id }}"
                            class="border-b odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $rejecteds->firstItem() + $key }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $rejected->product->nama_produk }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $rejected->stok }}
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($rejected->created_at)->format('d F Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700">Show</span>
                    <select wire:model.live="perPage" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">entries</span>
                </div>
                {{ $rejecteds->links() }}
            </div>
        </div>

        <div class="mt-6 text-xs text-gray-500 text-right">
            Loading Time: {{ $loadingTime }}s | RAM Usage: {{ $ramUsage }} MB | Data Size: {{ $dataSize }} KB | Total Records: {{ $rejecteds->total() }}
        </div>
    </div>
</div>
