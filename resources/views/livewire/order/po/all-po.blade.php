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
        <div class="px-2 py-5 mb-6 bg-white border rounded-xl md:px-7" wire:key='{{ $item->id }}'>
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
                            Rp.{{ $item->product_price }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Total Bayar <button type="button"
                                wire:click='showPaymentHistory({{ $item->id }})'
                                class="text-sm font-semibold text-blue-600">Lihat history pembayaran</button></p>
                        <p class="font-semibold">Rp.{{ $item->total_price }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Status Pembayaran</p>
                        <p class="font-semibold">{{ $item->po_status == 'close' ? 'Paid' : 'Unpaid' }}</p>
                    </div>
                </div>
                <div class="flex justify-between mt-5">
                    <div>
                        <p class="font-medium text-slate-500">Dibuat Oleh</p>
                        <p class="font-semibold">{{ $item->user->name }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Kurir</p>
                        <p class="font-semibold">{{ $item->expedition->nama_ekspedisi }}
                            (Rp.{{ $item->expedition_price }})
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Potongan deposit</p>
                        <p class="font-semibold">Rp.{{ $item->deposit_cut }}</p>
                    </div>
                </div>
                <div class="flex justify-between">
                    <div>
                        <x-button label="Print Invoice" class="rounded-xl" primary icon="receipt-tax" />
                        <x-button label="Print Label Pengiriman" class="rounded-xl" primary icon="truck" />
                    </div>
                    <div class="flex gap-5">
                        <x-button label="Update Pembayaran" class="items-center" primary icon="currency-dollar" />
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('po.editPo', [$order, $item->id]) }}"
                                class="px-4 py-2 text-sm font-medium text-blue-400 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                Edit order
                            </a>
                            <a
                                class="px-4 py-2 text-sm font-medium text-red-400 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                Cancel Order
                            </a>
                        </div>
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
    <x-modal.card title="History Pembayaran INV {{$selectedPoHistory ? $selectedPoHistory->invoice_code: ''}}" blur wire:model="paymentHistoryModal">
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
            @forelse ($paymentHistories as $payment)
                <div class="px-4 py-2 mt-2 border rounded-md" wire:key='{{ $payment->id }}'>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Nominal yang dibayarkan</p>
                            <p class="text-lg font-medium text-green-500">Rp.{{ $payment->amount }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal pembayaran</p>
                            <p class="text-lg font-medium ">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d F Y') }}</p>
                        </div>
                    </div>
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
                        Rp. {{ $selectedPoHistory->total_price - $paymentHistories->sum('amount') }}
                    </p>
                </div>
            </div>
        @endif



    </x-modal.card>
</div>
