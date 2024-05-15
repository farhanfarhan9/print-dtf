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
                Export Analyzer - Berdasarkan Order Customer
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <x-input wire:model.debounce.300ms="search" icon="search" placeholder="Cari Customer" class="w-full sm:w-1/4" shadowless="true" />
            <div class="flex space-x-4">
                <x-input wire:model.live="startDate" type="date" placeholder="Tanggal mulai" class="w-full" />
                <x-input wire:model.live="endDate" type="date" placeholder="Tanggal akhir" class="w-full" />
            </div>
            <x-button wire:click="exportExcel" label="Export" blue icon="download" class="w-full sm:w-auto" />
        </div>
                {{--  --}}
                <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
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
                                <th scope="col" class="px-6 py-3">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('jumlah_order')">
                                    Jumlah Order
                                    @if($sortField === 'jumlah_order')
                                        @if($sortDirection === 'asc')
                                            &#x25B2; <!-- Unicode for upward-pointing triangle -->
                                        @else
                                            &#x25BC; <!-- Unicode for downward-pointing triangle -->
                                        @endif
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('nama_customer')">
                                    Customer Name
                                    @if($sortField === 'nama_customer')
                                        @if($sortDirection === 'asc')
                                            &#x25B2;
                                        @else
                                            &#x25BC;
                                        @endif
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('frekuensi')">
                                    Frekuensi
                                    @if($sortField === 'frekuensi')
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
                            {{-- Edited 09 05 2024 --}}
                            {{-- {{ dd($customerOrders) }} --}}
                        @forelse ($customerOrders as $key  => $order)
                            <tr class="border-b odd:bg-white even:bg-gray-50 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $loop->index + 1 }}</td>
                                <td class="px-6 py-4">{{ $order['jumlah_order'] }}</td>
                                <td class="px-6 py-4">{{ $order['nama_customer'] }}</td>
                                <td class="px-6 py-4">{{ $order['frekuensi'] }}</td>
                                {{-- <td>{{ $order['newest_date']->format('d-m-Y H:i') }}</td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center">Data Kosong</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>
                {{-- <div class="mt-2">
                    {{ $PurchaseOrders->links() }}
                </div> --}}
    </div>
</div>

{{-- OLD CODE --}}
        {{--  --}}
        {{-- <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Jumlah Order
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama Customer
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Frekuensi
                        </th>
                        <!-- <th scope="col" class="px-6 py-3">
                            Alamat
                        </th> -->
                        <!-- <th scope="col" class="px-6 py-3">
                            No Telpon
                        </th> -->
                    </tr>
                </thead>
                <tbody>
                @forelse ($newCustomerOrders as $customerId  => $data)
                    <tr class="border-b odd:bg-white even:bg-gray-50 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $loop->index + 1 }}</td>
                        <td class="px-6 py-4">{{ $data['jumlah_order'] }}</td>
                        <td class="px-6 py-4">{{ $data['nama_customer'] }}</td>
                        <td class="px-6 py-4">{{ $data['frekuensi'] }}</td>
                        <td class="px-6 py-4">{{ $new_order['frekuensi'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center">Data Kosong</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div> --}}
        {{-- <div class="mt-2">
            {{ $PurchaseOrders->links() }}
        </div> --}}
{{-- OLD CODE --}}
