<div>
    @if (session('poCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('poCreated')[0] }}',
                    description: '{{ session('poCreated')[1] }}',
                    icon: '{{ session('poCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('poEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('poEdited')[0] }}',
                    description: '{{ session('poEdited')[1] }}',
                    icon: '{{ session('poEdited')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @endif

    <x-slot name="header">
        <div class="flex gap-4">
            <a href="{{ route('order.index') }}" wire:navigate
                class="flex items-center gap-1 p-2 mb-5 text-lg rounded-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Detail Order
            </h2>
        </div>
    </x-slot>
    @forelse ($purchase_orders as $item)
        <div class="px-2 py-5 mb-6 bg-white border rounded-xl md:px-7 {{ $item->po_status == 'close' ? 'border-green-600' : '' }}"
            wire:key='{{ $item->id }}'>
            <div class="flex justify-between pb-2 border-b">
                <p class="my-auto text-sm text-slate-500">Dibuat Pada
                    {{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}</p>
                <p>
                    INV {{ $item->invoice_code }}
                </p>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between mt-5">
                    <div>
                        <p class="font-medium text-slate-500">Produk</p>
                        <p class="font-semibold">{{ $item->product->nama_produk }} ({{ $item->qty }}m)
                            {{ rupiah_format($item->product_price) }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Total Bayar <button type="button"
                                wire:click='showPaymentHistory({{ $item->id }})'
                                class="text-sm font-semibold text-blue-600">Lihat history pembayaran</button></p>
                        <p class="font-semibold">{{ rupiah_format($item->total_price) }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Status Pembayaran</p>
                        @if ($item->po_status == 'close')
                            <p class="inline-block px-4 py-1 font-semibold text-white bg-green-400 rounded-lg">Paid</p>
                        @elseif($item->po_status == 'open')
                            <p class="inline-block px-4 py-1 font-semibold text-white bg-red-400 rounded-lg">Unpaid</p>
                        @endif
                        {{-- <p class="font-semibold">{{ $item->po_status == 'close' ? 'Paid' : 'Unpaid' }}</p> --}}
                    </div>
                </div>
                <div class="flex justify-between mt-5">
                    {{-- <div>
                        <p class="font-medium text-slate-500">Total yang sudah dibayarkan</p>
                        <p class="font-semibold">{{ rupiah_format($item->payments->sum('amount')) }}</p>
                    </div> --}}
                    <div>
                        <p class="font-medium text-slate-500">Kurir</p>
                        <p class="font-semibold">{{ $item->expedition->nama_ekspedisi }}
                            ({{ rupiah_format($item->expedition_price) }})
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Biaya tambahan</p>
                        <p class="font-semibold">Rp. {{ number_format($item->additional_price) }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Potongan deposit</p>
                        <p class="font-semibold">Rp. {{ number_format($item->deposit_cut, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Diskon</p>
                        <p class="font-semibold">Rp. {{ number_format($item->discount) }}</p>
                    </div>
                </div>
                <div>
                    <div>
                        <p class="font-medium text-slate-500">Dibuat oleh</p>
                        <p class="font-semibold">{{ $item->user->name }}</p>
                    </div>
                </div>
                <div class="flex justify-between">
                    <div>
                        <x-button wire:click="printInvoice({{ $item->id }})" label="Print Invoice"
                            class="rounded-xl" primary icon="receipt-tax" />
                        {{-- <x-button label="Print Label Pengiriman" class="rounded-xl" primary icon="truck" /> --}}
                        <x-button wire:click="printLabel({{ $item->id }})" label="Print Label Pengiriman"
                            class="rounded-xl" primary icon="truck" />

                    </div>
                    <div class="flex gap-5">
                        @if ($item->po_status == 'open')
                            <x-button wire:click='updatePaymentModal({{ $item->id }})' label="Update Pembayaran"
                                class="items-center" primary icon="currency-dollar" />
                        @else
                            <x-button label="Update Pembayaran" disabled class="items-center" secondary
                                icon="currency-dollar" />
                        @endif
                        {{-- <div class="inline-flex rounded-md shadow-sm" role="group"> --}}
                        <button type="button" wire:click='deleteDialog({{ $item->id }})'
                            class="px-4 py-2 text-sm font-medium text-red-400 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                            Cancel Order
                        </button>
                        {{-- <button type="button" wire:click="deleteDialog({{ $item->id }})" class="px-4 py-2 text-sm font-medium text-red-400 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700" label="Cancel Order" red </button> --}}

                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    @empty
        No Data
    @endforelse
    <div class="mt-2">
        {{ $purchase_orders->links() }}
    </div>
    <x-modal.card title="History Pembayaran INV {{ $selectedPoHistory ? $selectedPoHistory->invoice_code : '' }}" blur
        wire:model="paymentHistoryModal">
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
                        {{ rupiah_format($selectedPoHistory->total_price - $paymentHistories->sum('amount')) }}
                    </p>
                </div>
            </div>
        @endif



    </x-modal.card>

    {{--  --}}
    <x-modal.card title="Pembayaran INV {{ $selectedPo ? $selectedPo->invoice_code : '' }}" blur
        wire:model="paymentModal">
        @if ($selectedPo)
            @php
                $remainingPayment = $selectedPo->total_price - $selectedPo->payments->sum('amount');
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
                        <x-select label="Detail bank" placeholder="Detail bank" :options="['BRI', 'BCA', 'BNI', 'CASH']"
                            wire:model.live="bank_detail" />
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
                        wire:click="updatePayment({{ $selectedPo->id }})" />
                </div>
            </x-slot>
        @endif
    </x-modal.card>
</div>
