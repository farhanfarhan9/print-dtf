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
        <div class="flex flex-wrap items-center justify-between mb-6">
            <div class="flex space-x-4">
                <x-input wire:model.live.debounce.300ms="search" placeholder="Search product name..." class="w-64" />
            </div>
            <div class="flex space-x-4">
                <x-input wire:model.live="startDate" type="date" placeholder="dd/mm/yyyy" class="w-44" />
                <x-input wire:model.live="endDate" type="date" placeholder="dd/mm/yyyy" class="w-44" />
                <x-button wire:click="exportExcel" blue wire:loading.attr="disabled" wire:target="exportExcel" class="px-10 py-2.5">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span wire:loading.remove wire:target="exportExcel">Export</span>
                        <span wire:loading wire:target="exportExcel">Exporting...</span>
                    </div>
                </x-button>
                <x-button href="{{ route('rejected-products.create') }}" green class="px-8 py-2.5">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Data
                    </div>
                </x-button>
            </div>
        </div>

        <div wire:loading.delay class="w-full text-center py-4">
            <div class="flex justify-center items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading...</span>
            </div>
        </div>

        <div wire:loading.remove class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            NO
                        </th>
                        <th scope="col" class="px-6 py-3">
                            NAMA PRODUK
                        </th>
                        <th scope="col" class="px-6 py-3">
                            STOK
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TANGGAL
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

        <div class="flex flex-wrap items-center justify-between mt-4">
            <div class="flex items-center space-x-1">
                <span class="text-sm text-gray-700">Show</span>
                <select wire:model.live="perPage" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700">entries</span>
            </div>

            <div>
                {{ $rejecteds->links() }}
            </div>
        </div>

        <div class="mt-6 text-xs text-gray-500 text-right">
            Loading Time: {{ $loadingTime }}s | RAM Usage: {{ $ramUsage }} MB | Data Size: {{ $dataSize }} KB | Total Records: {{ $rejecteds->total() }}
        </div>
    </div>
</div>
