@if (session('exportSuccess'))
    <script>
        Wireui.hook('notifications:load', () => {
            window.$wireui.notify({
                title: '{{ session('exportSuccess')[0] }}',
                description: '{{ session('exportSuccess')[1] }}',
                icon: '{{ session('exportSuccess')[2] }}',
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
                Export Pembukuan - Berdasarkan Penjualan Harian/Bulanan
            </h2>
        </div>
    </x-slot>
    <div class="pt-12">
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 sm:space-x-4">
            <x-input wire:model.debounce.300ms="search" icon="search" placeholder="Cari Data Penjualan"
                class="w-full sm:w-1/4" shadowless="true" />

            <div class="flex space-x-2">
                <!-- Toggle Buttons for Daily and Monthly View -->
                <x-button wire:click="switchToDaily"
                    class="{{ $viewMode == 'daily' ? 'bg-blue-500 text-white hover:bg-gray-300 hover:text-black' : 'bg-gray-300 text-black hover:bg-blue-500 hover:text-white' }} w-full sm:w-auto">Harian</x-button>
                <x-button wire:click="switchToMonthly"
                    class="{{ $viewMode == 'monthly' ? 'bg-blue-500 text-white hover:bg-gray-300 hover:text-black' : 'bg-gray-300 text-black hover:bg-blue-500 hover:text-white' }} w-full sm:w-auto">Bulanan</x-button>
            </div>

            <div class="flex space-x-4">
                <x-input wire:model.live="startDate" type="date" placeholder="Tanggal mulai"
                    class="w-full {{ $viewMode == 'monthly' ? 'hidden' : '' }}" />
                <x-input wire:model.live="startDate" type="month" placeholder="Bulan mulai"
                    class="w-full {{ $viewMode == 'daily' ? 'hidden' : '' }}" />

                <x-input wire:model.live="endDate" type="date" placeholder="Tanggal akhir"
                    class="w-full {{ $viewMode == 'monthly' ? 'hidden' : '' }}" />
                <x-input wire:model.live="endDate" type="month" placeholder="Bulan akhir"
                    class="w-full {{ $viewMode == 'daily' ? 'hidden' : '' }}" />
            </div>
            <x-button wire:click="exportExcel" label="Export" blue icon="download" class="w-full sm:w-auto" />
        </div>
    </div>
    @if ($viewMode == 'daily')
        {{-- Daily --}}
        <div class="pt-12">
            @forelse ($dailyGroupPurchases as $purchase_date => $dailyPurchase)
                <div class="mb-10" wire:key='key-{{ \Carbon\Carbon::parse($purchase_date)->isoFormat('D-MM-YYYY') }}'>
                    <x-card>
                        @php
                            $totalPricePerDay = 0;
                        @endphp
                        <div class="mb-2 text-xl font-purchase_date">Pembukuan
                            @php
                                \Carbon\Carbon::setLocale('id');
                            @endphp
                            {{ \Carbon\Carbon::parse($purchase_date)->isoFormat('dddd, D MMMM YYYY') }}
                        </div>
                        {{--  --}}
                        <div class="p-5 mb-5 border rounded-md">
                            <table
                                class="w-full mb-8 text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 rounded-s-lg">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Nama Customer
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Uang Masuk
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Bank
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Tanggal dan Waktu
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dailyPurchase as $key => $purchaseData)
                                        @php
                                            $totalPricePerDay += $purchaseData['amount'];
                                        @endphp
                                        <tr wire:key="key-{{ $key }}"
                                            class=" odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $key + 1 }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $purchaseData['customer_name'] }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ rupiah_format($purchaseData['amount']) }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $purchaseData['bank_detail'] }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $purchaseData['purchase_time'] }}
                                            </th>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center">Data Kosong</td>
                                        </tr>
                                    @endforelse
                                    <tr wire:key="key-{{ $key }}"" class="text-white bg-green-600">
                                        <th
                                            class="px-6 py-2 font-bold text-center text-medium rounded-s-lg whitespace-nowrap">
                                            Total
                                        </th>
                                        <th class="px-6 py-2 font-bold text-medium whitespace-nowrap">
                                            {{ count($dailyPurchase) }} Customer
                                        </th>
                                        <th colspan="3"
                                            class="px-6 py-2 font-bold text-medium rounded-e-lg whitespace-nowrap">
                                            {{ rupiah_format($totalPricePerDay) }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            {{-- Don't Need it, but i will save it here. Just in case ¯\_(ツ)_/¯ --}}
                            {{-- <p class="mb-2 text-xl font-semibold">Total Keseluruhan shift 2</p>
                                    <p class="text-lg font-semibold">Total Panjang = {{ "1" }}</p>
                                    <p class="text-lg font-semibold">Total Harga = {{ "1" }} --}}
                            </p>
                        </div>
                        {{--  --}}
                    </x-card>
                </div>
            @empty
                No Data
            @endforelse
        </div>
        {{-- Daily --}}
    @elseif($viewMode == 'monthly')
        {{-- Monthly --}}
        <div class="pt-12">
            @forelse ($monthlyGroupPurchases as $purchase_month => $monthlyPurchase)
                <div class="mb-10"
                    wire:key='key-{{ \Carbon\Carbon::createFromFormat('Y-m', $purchase_month)->isoFormat('MM-YYYY') }}'>
                    <x-card>
                        @php
                            $totalPricePerMonth = 0;
                        @endphp
                        <div class="mb-2 text-xl font-purchase_month">Pembukuan Bulanan
                            @php
                                \Carbon\Carbon::setLocale('id');
                            @endphp
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $purchase_month)->isoFormat('MMMM YYYY') }}
                        </div>
                        <div class="p-5 mb-5 border rounded-md">
                            <table
                                class="w-full mb-8 text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 rounded-s-lg">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Nama Customer
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Uang Masuk
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Bank
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Tanggal dan Waktu
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($monthlyPurchase as $key => $purchaseData)
                                        @php
                                            $totalPricePerMonth += $purchaseData['amount'];
                                        @endphp
                                        <tr wire:key="key-{{ $key }}"
                                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $key + 1 }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $purchaseData['customer_name'] }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ rupiah_format($purchaseData['amount']) }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $purchaseData['bank_detail'] }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $purchaseData['purchase_time'] }}
                                            </th>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center">Data Kosong</td>
                                        </tr>
                                    @endforelse
                                    <tr class="text-white bg-green-600">
                                        <th
                                            class="px-6 py-2 font-bold text-center text-medium rounded-s-lg whitespace-nowrap">
                                            Total
                                        </th>
                                        <th class="px-6 py-2 font-bold text-medium whitespace-nowrap">
                                            {{ count($monthlyPurchase) }} Customer
                                        </th>
                                        <th colspan="3"
                                            class="px-6 py-2 font-bold text-medium rounded-e-lg whitespace-nowrap">
                                            {{ rupiah_format($totalPricePerMonth) }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
            @empty
                <div>No Data</div>
            @endforelse
        </div>
        {{-- Monthly --}}
    @else
        {{-- Default --}}
        <div class="py-12">
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
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">{{ $purchase['customer_name'] }}</td>
                                <td class="px-6 py-4">{{ rupiah_format($purchase['amount']) }}</td>
                                <td class="px-6 py-4">{{ $purchase['bank_detail'] }}</td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($purchase['purchase_date'])->isoFormat('dddd, DD-MM-YYYY, H:mm:s') }}
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
            {{-- <div class="mt-4">
                {{ $dailyPurchases->links() }}
            </div> --}}
        </div>
        {{-- Default --}}
    @endif
</div>
