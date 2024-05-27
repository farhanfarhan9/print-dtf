<div>
    @if (session('orderCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('orderCreated')[0] }}',
                    description: '{{ session('orderCreated')[1] }}',
                    icon: '{{ session('orderCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('orderEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('orderEdited')[0] }}',
                    description: '{{ session('orderEdited')[1] }}',
                    icon: '{{ session('orderEdited')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('orderCanceled'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('orderCanceled')[0] }}',
                    description: '{{ session('orderCanceled')[1] }}',
                    icon: '{{ session('orderCanceled')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @endif

    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Order
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex flex-col sm:flex-row sm:justify-between">
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari Customer" />
            <x-button label="Tambah Order" href="{{ route('order.create') }}" class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green
                icon="plus" />
        </div>
    </div>
    @forelse ($purchases as $purchase)
        <div class="px-2 py-5 mb-6 bg-white border rounded-xl md:px-7" wire:key="{{ $purchase->id }}">
            <div class="flex justify-between pb-2 border-b">
                <p class="my-auto text-sm text-slate-500">Dibuat Pada
                    {{ \Carbon\Carbon::parse($purchase->created_at)->format('d F Y') }}</p>
                @if ($purchase->payment_status == 'close' || $purchase->payment_status == 'open' && $purchase->purchase_orders->count() == 0)
                    <button type="button" wire:click='deleteDialog({{ $purchase->id }})'
                        class="px-4 py-2 text-sm font-medium text-red-400 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                        Cancel Order
                    </button>
                @endif
                {{-- <div>
                    <p>
                        INV 2024.01.15.1230943
                    </p>
                </div> --}}
            </div>
            <div class="space-y-3">
                <div class="flex justify-between mt-5">
                    <div>
                        <p class="font-medium text-slate-500">Pemesan</p>
                        <p class="font-semibold">{{ $purchase->customer->name }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Total Bayar <button type="button"
                                wire:click='showPaymentHistory({{ $purchase->id }})'
                                class="text-sm font-semibold text-blue-600">Lihat history pembayaran</button></p>
                        <p class="font-semibold">
                            <span class="px-2 py-1 text-white bg-red-500 rounded-md">
                                {{ rupiah_format($purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price')) }}
                            </span>

                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Total yang sudah dibayarkan</p>
                        <p class="font-semibold">
                            <span class="px-2 py-1 text-white bg-yellow-500 rounded-md">
                                {{ rupiah_format($purchase->payments->sum('amount')) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Status pembayaran</p>
                        <p class="font-semibold">
                            @php
                                $poStatuses = $purchase->payment_status;
                            @endphp
                            @if ($purchase->purchase_orders->count() == 1 && $purchase->purchase_orders[0]->status == 'cancel')
                                <p class="inline-block px-4 py-1 font-semibold text-white bg-yellow-400 rounded-lg">
                                    Cancel</p>
                            @else
                                @if ($poStatuses == 'open')
                                    <p class="inline-block px-4 py-1 font-semibold text-white bg-red-400 rounded-lg">
                                        Unpaid</p>
                                @else
                                    <p class="inline-block px-4 py-1 font-semibold text-white bg-green-400 rounded-lg">
                                        Paid</p>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex gap-10">
                    <div>
                        <p class="font-medium text-slate-500">Jumlah order</p>
                        <p class="font-semibold">
                            {{ count($purchase->purchase_orders->where('status', '!=', 'cancel')) }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Sisa yang harus dibayarkan</p>
                        <p class="font-semibold ">
                            <span class="px-2 py-1 text-white bg-green-600 rounded-md">
                                {{-- @dump($purchase->purchase_orders->where('status', '!=', 'cancel')->count() != 0) --}}
                                @if ($purchase->purchase_orders->where('status', '!=', 'cancel')->count() != 0)
                                    {{ rupiah_format($purchase->total_payment - $purchase->payments->sum('amount')) }}
                                @else
                                    0
                                @endif
                                {{-- {{ rupiah_format($purchase->purchase_orders->where('status', '!=', 'cancel')->sum('total_price')) }} --}}

                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex justify-between w-full">
                    <!-- Left-aligned buttons -->
                    <div class="flex gap-5">
                        @if (count($purchase->purchase_orders->where('status', '!=', 'cancel')) != 0)
                            <a href="{{ route('print.invoice.label', ['orderId' => $purchase->id]) }}" target="_blank">
                                <x-button label="Print Invoice" class="rounded-xl" primary icon="receipt-tax" />
                            </a>
                            <a href="{{ route('print.shipping.label', ['orderId' => $purchase->id]) }}"
                                target="_blank">
                                <x-button label="Print Label Pengiriman" class="rounded-xl" primary icon="truck" />
                            </a>
                        @endif

                    </div>
                    {{-- <div class="flex gap-5">
                        <x-button wire:click="printInvoice({{ $purchase->id }})" label="Print Invoice"
                            class="rounded-xl" primary icon="receipt-tax" />
                        <x-button wire:click="printLabel({{ $purchase->id }})" label="Print Label Pengiriman"
                            class="rounded-xl" primary icon="truck" />
                    </div> --}}

                    <!-- Right-aligned buttons -->
                    <div class="flex gap-5">
                        @if ($purchase->payment_status == 'open')
                            <x-button wire:click='updatePaymentModal({{ $purchase->id }})' label="Update Pembayaran"
                                class="items-center" primary icon="currency-dollar" />
                        @else
                            <x-button label="Update Pembayaran" disabled class="items-center" secondary
                                icon="currency-dollar" />
                        @endif
                        @if ($purchase->purchase_orders->where('status', '!=', 'cancel')->count() != 0)
                            <x-button href="{{ route('po.allPo', $purchase->id) }}" label="Detail order" primary
                                icon="tag" />
                        @else
                            <x-button label="Detail order" disabled secondary icon="tag" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        No Data
    @endforelse
    <div class="mt-2">
        {{ $purchases->links() }}
    </div>

    <x-modal.card title="History Pembayaran INV " blur wire:model="paymentHistoryModal">
        {{-- <x-input type="number" class="!pl-[2.5rem]" label="Jumlah deposit" prefix="Rp." wire:model="newDeposit" />

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Simpan" wire:click="addDeposit" />
                </div>
            </div>
        </x-slot> --}}
        @if ($paymentHistories)
            @forelse ($paymentHistories as $key=>$payment)
                <div class="px-4 py-2 mt-2 border rounded-md" wire:key='{{ $payment->id }}'>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-lg font-semibold">Pembayaran ke-{{ $key + 1 }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold">{{ $payment->bank_detail }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Nominal yang dibayarkan</p>
                            <p class="text-lg font-medium text-green-500">{{ rupiah_format($payment->amount) }}
                                {{ $payment->is_dp ? '(DP)' : '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal pembayaran</p>
                            <p class="text-lg font-medium ">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d F Y') }}</p>
                        </div>
                    </div>
                    @if ($payment->file)
                        <div>
                            {{-- {{$payment}} --}}
                            <p class="text-sm text-gray-500">Bukti Pembayaran</p>
                            <a href="{{ asset('storage/' . $payment->file) }}" target="_blank">
                                <img src="{{ asset('storage/' . $payment->file) }}" class="object-scale-down w-1/2"
                                    alt="">
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text">
                    Belum ada History Pembayaran
                </div>
            @endforelse
            <div class="flex justify-end mt-2 text-right">
                <div>
                    <p class="text-sm text-gray-500">Sisa yang harus dibayarkan</p>
                    <p class="text-sm text-gray-500">
                        {{ rupiah_format($selectedHistory->total_payment - $paymentHistories->sum('amount')) }}
                    </p>
                </div>
            </div>
        @endif
    </x-modal.card>

    <x-modal.card title="Pembayaran INV {{ $selectedPurchase ? $selectedPurchase->invoice_code : '' }}" blur
        wire:model="paymentModal">
        @if ($selectedPurchase)
            @php
                $remainingPayment = $selectedPurchase->total_payment - $selectedPurchase->payments->sum('amount');
            @endphp
            <div>
                <x-inputs.currency label="Nominal pembayaran *" max="{{ $remainingPayment }}"
                    placeholder="Nominal pembayaran" wire:model="amount" />

                <div class="flex justify-between gap-5 mt-5">
                    <div class="w-1/2">
                        <x-input-label>Bukti pembayaran</x-input-label>
                        <x-input-file wire:model='file'></x-input-file>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>
                    <div class="w-1/2">
                        <x-select label="Detail bank *" placeholder="Detail bank" :options="['BRI', 'BCA', 'BNI', 'CASH']"
                            wire:model="bank_detail" />
                    </div>
                </div>
                @if ($file)
                    <img src="{{ $file->temporaryUrl() }}" class="object-scale-down w-1/2" alt="">
                @endif
            </div>

            <div class="flex justify-end mt-2 text-right">
                <div>
                    <p class="text-sm text-gray-500">Sisa yang harus dibayarkan</p>
                    <p class="text-sm text-gray-500">
                        {{ rupiah_format($remainingPayment) }}
                    </p>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <button type="button" wire:loading wire:target="file"
                        class="px-4 py-2 text-sm font-semibold text-center text-white align-middle bg-gray-500 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 mx-auto" viewBox="0 0 200 200">
                            <circle fill="#FFFFFF" stroke="#FFFFFF" stroke-width="15" r="15" cx="40"
                                cy="65">
                                <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;"
                                    keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4">
                                </animate>
                            </circle>
                            <circle fill="#FFFFFF" stroke="#FFFFFF" stroke-width="15" r="15" cx="100"
                                cy="65">
                                <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;"
                                    keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2">
                                </animate>
                            </circle>
                            <circle fill="#FFFFFF" stroke="#FFFFFF" stroke-width="15" r="15" cx="160"
                                cy="65">
                                <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;"
                                    keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0">
                                </animate>
                            </circle>
                        </svg>
                    </button>
                    <x-button primary wire:target="file" wire:loading.remove label="Simpan"
                        wire:click="updatePayment({{ $selectedPurchase->id }})" />
                </div>
            </x-slot>
        @endif
    </x-modal.card>
</div>
