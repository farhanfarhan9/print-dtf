<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Internal Process
            </h2>
        </div>
    </x-slot>
    <div class="pt-12">
        @forelse ($internals as $execution_date => $internalProcesses)
            <div class="mb-10" wire:key='key-{{ $execution_date }}'>
                <x-card>
                    <div class="mb-2 text-xl font-semibold">Batch
                        {{ \Carbon\Carbon::parse($execution_date)->format('d F Y') }}</div>
                    @php
                        $byShift = $internalProcesses->groupBy('shift_no')->sortBy(function ($value, $key) {
                            // Sorting by machine_no, putting empty string first
                            return $key === '' ? -1 : $key;
                        });

                    @endphp
                    @foreach ($byShift as $shift => $shiftProcesses)
                        <div class="p-5 mb-5 border rounded-md">
                            @if ($shift != null)
                                <p class="mb-2 text-xl font-semibold">Shift {{ $shift }}</p>
                            @endif
                            @foreach ($shiftProcesses->groupBy('machine_no') as $machine => $processes)
                                @if ($machine != null)
                                    <p class="mb-2 font-medium">Mesin {{ $machine }}</p>
                                @else
                                    <p class="mb-2 font-medium">Belum ada Mesin</p>
                                @endif
                                <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
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
                                                Panjang
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Harga
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                RIP
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Print
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Packing
                                            </th>
                                            <th scope="col" class="px-6 py-3 rounded-e-lg">
                                                Konfirmasi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQty = 0;
                                        @endphp
                                        @foreach ($processes as $key => $internal)
                                            @if ($internal->is_confirm == 0)
                                            @php
                                                $totalQty += $internal->purchase_order->qty;
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
                                                    <th scope="row"
                                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        @if ($internal->machine_no == null)
                                                            <x-button positive label="RIP"
                                                                wire:click='ripDialog({{ $internal->id }})' />
                                                        @else
                                                            <x-button icon="check" secondary label="Done"
                                                                disabled />
                                                        @endif

                                                    </th>
                                                    <th scope="row"
                                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        @if ($internal->machine_no && $internal->print_no == null)
                                                            <form action="" method="post"
                                                                wire:submit='addPrintNo({{ $internal }})'>
                                                                <div class="w-12 mx-2 mb-1">
                                                                    <x-input wire:model="printNos.{{ $internal->id }}"
                                                                        placeholder="No urut" />
                                                                </div>
                                                                <x-button type="submit" positive spinner
                                                                    label="Submit" />
                                                            </form>
                                                        @elseif($internal->machine_no && $internal->print_no)
                                                            {{ $internal->print_no }}
                                                        @else
                                                            Tidak tersedia
                                                        @endif
                                                    </th>

                                                    <th scope="row"
                                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        @if ($internal->machine_no && $internal->print_no && $internal->is_done == null)
                                                            <x-button positive label="Selesai"
                                                                wire:click='doneProcess({{ $internal->id }})' />
                                                        @elseif($internal->machine_no && $internal->print_no && $internal->is_done)
                                                            <x-button icon="check" secondary label="Done"
                                                                disabled />
                                                        @else
                                                            Tidak Tersedia
                                                        @endif
                                                    </th>
                                                    <th scope="row"
                                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        @if ($internal->machine_no && $internal->print_no && $internal->is_done && $internal->is_confirm == null)
                                                            @if (Auth::user()->roles == 'admin')
                                                                <x-button positive label="Selesai"
                                                                    wire:click='confirmProcess({{ $internal->id }})' />
                                                            @else
                                                                Menunggu konfirmasi admin
                                                            @endif
                                                        @elseif($internal->machine_no && $internal->print_no && $internal->is_done && $internal->is_confirm)
                                                            <x-button icon="check" secondary label="Done"
                                                                disabled />
                                                        @else
                                                            Tidak Tersedia
                                                        @endif
                                                    </th>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td colspan="3" class="text-xl font-bold text-center">Total</td>
                                            <td colspan="3" class="text-xl font-bold text-center">{{$totalQty}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endforeach
                            {{-- <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
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
                                            Jumlah
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Harga
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            RIP
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Print
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Packing
                                        </th>
                                        <th scope="col" class="px-6 py-3 rounded-e-lg">
                                            Konfirmasi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shiftProcesses->groupBy('machine_no') as $machine => $processes)
                                        @foreach ($processes as $key => $internal)
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
                                                    {{ $internal->purchase_order->user->name }}
                                                </th>
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{ $internal->purchase_order->qty }}
                                                </th>
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{ rupiah_format($internal->purchase_order->total_price) }}
                                                </th>
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    @if ($internal->machine_no == null)
                                                        <x-button positive label="RIP"
                                                            wire:click='ripDialog({{ $internal->id }})' />
                                                    @else
                                                        <x-button icon="check" secondary label="Done" disabled />
                                                    @endif

                                                </th>
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    @if ($internal->machine_no && $internal->print_no == null)
                                                        <x-button positive label="Print"
                                                            wire:click='ripDialog({{ $internal->id }})' />
                                                    @else
                                                        Tidak tersedia
                                                    @endif
                                                </th>
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    Jon
                                                </th>
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    Jon
                                                </th>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table> --}}
                        </div>
                    @endforeach
                    {{-- @php
                        $byMachines = $byShift->groupBy('machine_no')->sortBy(function ($value, $key) {
                            // Sorting by machine_no, putting empty string first
                            return $key === '' ? -1 : $key;
                        });
                    @endphp
                    @foreach ($byMachines as $key => $byMachine)
                        <div class="p-5 mb-5 border rounded-md">
                            @if ($key != null)
                                <p class="mb-2 font-medium">Mesin ke-{{ $key }}</p>
                            @else
                                <p class="mb-2 font-medium">Belum ada Mesin</p>
                            @endif
                            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
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
                                            Jumlah
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Harga
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            RIP
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Print
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Packing
                                        </th>
                                        <th scope="col" class="px-6 py-3 rounded-e-lg">
                                            Konfirmasi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($byMachine as $key => $internal)
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
                                                {{ $internal->purchase_order->user->name }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $internal->purchase_order->qty }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ rupiah_format($internal->purchase_order->total_price) }}
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                @if ($internal->machine_no == null)
                                                    <x-button positive label="RIP"
                                                        wire:click='ripDialog({{ $internal->id }})' />
                                                @else
                                                    <x-button icon="check" secondary label="Done" disabled />
                                                @endif

                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                @if ($internal->machine_no && $internal->print_no == null)
                                                    <x-button positive label="Print"
                                                        wire:click='ripDialog({{ $internal->id }})' />
                                                @else
                                                    Tidak tersedia
                                                @endif
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                Jon
                                            </th>
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                Jon
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach --}}
                </x-card>
            </div>
        @empty
            No Data
        @endforelse

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
