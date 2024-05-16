<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                History Internal Process
            </h2>
        </div>
    </x-slot>
    <div class="pt-12">
        @forelse ($internals as $execution_date => $internalProcesses)
            <div class="mb-10" wire:key='key-{{ $execution_date }}'>
                <x-card>
                    @php
                        $totalQtyPerDay = 0;
                        $totalPricePerDay = 0;
                    @endphp
                    <div class="mb-2 text-xl font-semibold">Batch
                        {{ \Carbon\Carbon::parse($execution_date)->format('d F Y') }}</div>
                    @php
                        $byShift = $internalProcesses->groupBy('shift_no')->sortBy(function ($value, $key) {
                            // Sorting by machine_no, putting empty string first
                            return $key === '' ? -1 : $key;
                        });

                    @endphp
                    @foreach ($byShift as $shift => $shiftProcesses)
                        @if ($shift != null)
                            <div class="p-5 mb-5 border rounded-md">
                                <p class="mb-2 text-xl font-semibold">Shift {{ $shift }}</p>
                                @php
                                    $totalQtyPerShift = 0;
                                    $totalPricePerShift = 0;
                                @endphp
                                @foreach ($shiftProcesses->groupBy('machine_no') as $machine => $processes)
                                    @if ($machine != null)
                                        <p class="mb-2 font-medium">Mesin {{ $machine == 1 ? 'Ino' : 'Magna' }}</p>
                                        <table
                                            class="w-full mb-8 text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 rounded-s-lg">
                                                        No
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">
                                                        Invoice
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">
                                                        Pemesan
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">
                                                        Panjang (m)
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">
                                                        Harga
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalQty = 0;
                                                    $totalPrice = 0;
                                                @endphp
                                                @foreach ($processes as $key => $internal)
                                                    @if ($internal->is_confirm)
                                                        @php
                                                            $totalQty += $internal->purchase_order->qty;
                                                            $totalPrice += $internal->purchase_order->total_price;
                                                        @endphp
                                                        <tr wire:key="key-{{ $key }}"
                                                            class=" odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                                                            <th scope="row"
                                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                {{ $key + 1 }}
                                                            </th>
                                                            <th scope="row"
                                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                {{ $internal->purchase_order->invoice_code }}
                                                            </th>
                                                            <th scope="row"
                                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                {{ $internal->purchase_order->purchase->customer->name }}
                                                            </th>
                                                            <th scope="row"
                                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                {{ $internal->purchase_order->qty }}
                                                            </th>
                                                            <th scope="row"
                                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                {{ rupiah_format($internal->purchase_order->total_price) }}
                                                            </th>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                <tr class="text-white bg-green-600 ">
                                                    <td colspan="3"
                                                        class="text-xl font-bold text-center rounded-s-lg">Total</td>
                                                    <td class="text-xl font-bold">
                                                        <span class="">{{ $totalQty }}</span>
                                                    </td>
                                                    <td class="text-xl font-bold rounded-e-lg">
                                                        {{ rupiah_format($totalPrice) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        @php
                                            $totalQtyPerShift += $totalQty;
                                            $totalPricePerShift += $totalPrice;
                                        @endphp
                                    @endif
                                @endforeach
                                @php
                                    $totalQtyPerDay += $totalQtyPerShift;
                                    $totalPricePerDay += $totalPricePerShift;
                                @endphp
                                <p class="mb-2 text-xl font-semibold">Total Keseluruhan Shift {{ $shift }}</p>
                                <p class="text-lg font-semibold">Total Panjang = {{ $totalQtyPerShift }}</p>
                                <p class="text-lg font-semibold">Total Harga = {{ rupiah_format($totalPricePerShift) }}
                                </p>
                            </div>
                        @endif
                    @endforeach
                    <p class="mb-2 text-xl font-semibold">Total Keseluruhan
                        {{ \Carbon\Carbon::parse($execution_date)->format('d F Y') }}</p>
                    <p class="text-lg font-semibold">Total Panjang = {{ $totalQtyPerDay }}</p>
                    <p class="text-lg font-semibold">Total Harga = {{ rupiah_format($totalPricePerDay) }}</p>
                </x-card>

            </div>
        @empty
            No Data
        @endforelse
        {{-- <div class="mt-2">
            {{ $internals->links() }}
        </div> --}}

        {{-- RIP MODAL --}}
        <x-modal.card
            title="RIP invoice: {{ isset($selectedData) ? $selectedData->purchase_order->invoice_code : '' }}" blur
            wire:model.defer="ripModal">
            <x-native-select label="Nomor Mesin" placeholder="Pilih Nomor Mesin" :options="[1, 2]"
                wire:model="machineNo" />

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <div class="flex">
                        <x-button flat label="Cancel" x-on:click="close" />
                        <x-button primary label="Simpan" wire:click="addMachineNo" />
                    </div>
                </div>
            </x-slot>
        </x-modal.card>
    </div>
</div>
