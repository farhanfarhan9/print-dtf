<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Debt Customer
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="flex justify-between">
            <x-button wire:click="exportExcel" label="Export to Excel" green icon="download" />
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari Customer" />
        </div>

        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <style>
                        th {
                            position: relative;
                            padding-right: 20px; /* Ensure space for the icon */
                        }
                        th:after {
                            position: absolute;
                            right: 8px;
                            top: 50%;
                            transform: translateY(-50%);
                        }
                    </style>
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Customer ID</th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('customer_name')">
                            Customer Name
                            @if($sortField === 'customer_name')
                                @if($sortDirection === 'asc')
                                    &#x25B2;
                                @else
                                    &#x25BC;
                                @endif
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('remaining_debt')">
                            Sisa Hutang
                            @if($sortField === 'remaining_debt')
                                @if($sortDirection === 'asc')
                                    &#x25B2;
                                @else
                                    &#x25BC;
                                @endif
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('last_payment_date')">
                            Tanggal Terakhir Bayar
                            @if($sortField === 'last_payment_date')
                                @if($sortDirection === 'asc')
                                    &#x25B2;
                                @else
                                    &#x25BC;
                                @endif
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debtCustomers as $index => $debtCustomer)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4">{{ ($debtCustomers->currentPage() - 1) * $debtCustomers->perPage() + $loop->index + 1 }}</td>
                            <td class="px-6 py-4">{{ $debtCustomer->customer_id }}</td>
                            <td class="px-6 py-4">{{ $debtCustomer->customer_name }}</td>
                            <td class="px-6 py-4">{{ rupiah_format($debtCustomer->total_debt - $debtCustomer->total_paid) }}</td>
                            <td class="px-6 py-4">
                                {{ $debtCustomer->last_payment_date ? date('d M Y', strtotime($debtCustomer->last_payment_date)) : 'Belum ada pembayaran' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center">Tidak ada data hutang customer</td>
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
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">entries</span>
                </div>
                {{ $debtCustomers->links() }}
            </div>
        </div>

        <div class="mt-6 text-xs text-gray-500 text-right">
            @php
                $startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
                $loadTime = round((microtime(true) - $startTime), 2);
                $ramUsage = round(memory_get_usage() / 1024 / 1024, 2);

                // Calculate data size more efficiently
                $dataSize = 0;
                if (isset($debtCustomers) && $debtCustomers->count() > 0) {
                    // Estimate data size based on a sample of the first item
                    $sampleSize = strlen(json_encode($debtCustomers->first()));
                    $dataSize = round(($sampleSize * $debtCustomers->count()) / 1024, 2);
                }
            @endphp
            Loading Time: {{ $loadTime }} seconds | RAM Usage: {{ $ramUsage }} MB | Data Size: {{ $dataSize }} KB
        </div>
    </div>
</div>
