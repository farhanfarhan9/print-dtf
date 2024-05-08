@if (session('exportSuccess'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{session('exportSuccess')[0]}}',
                    description: '{{session('exportSuccess')[1]}}',
                    icon: '{{session('exportSuccess')[2]}}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('exportFailed'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: 'Gagal',
                    description: 'Salah satu Tanggal Belum di isi',
                    icon: 'error',
                    timeout: 3000
                })
            })
        </script>
    @endif
<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Export Pembukuan - Berdasarkan Penjualan Harian
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <x-input wire:model.debounce.300ms="search" icon="search" placeholder="Cari Data Penjualan" class="w-full sm:w-1/4" shadowless="true" />
            <div class="flex space-x-4">
                <x-input wire:model.live="startDate" type="date" placeholder="Tanggal mulai" class="w-full" />
                <x-input wire:model.live="endDate" type="date" placeholder="Tanggal akhir" class="w-full" />
            </div>
            <x-button wire:click="exportExcel" label="Export" blue icon="download" class="w-full sm:w-auto" />
        </div>
        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama Customer
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Harga
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Bank
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tanggal Pembelian
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        \Carbon\Carbon::setLocale('id');
                    @endphp
                @forelse ($dailyPurchases as $index => $purchase)
                    <tr class="border-b odd:bg-white even:bg-gray-50 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">{{ $purchase['customer_name'] }}</td>
                        <td class="px-6 py-4">{{ rupiah_format($purchase['amount']) }}</td>
                        <td class="px-6 py-4">{{ $purchase['bank_detail'] }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($purchase['purchase_date'])->isoFormat('dddd, DD-MM-YYYY, H:mm:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center">Data Kosong</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
        {{-- <div class="mt-4">
            {{ $dailyPurchases->links() }}
        </div> --}}
    </div>
</div>
