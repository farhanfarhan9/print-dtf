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
                        <p class="font-medium text-slate-500">Total Bayar</p>
                        <p class="font-semibold">{{ $purchase->purchase_orders->sum('total_price') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Status pembayaran</p>
                        <p class="font-semibold">
                            @php
                                $poStatuses = $purchase->purchase_orders->pluck('po_status');
                            @endphp
                            @if ($poStatuses->contains('open'))
                                Unpaid
                            @else
                                Paid
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex justify-between">
                    <div>
                        <p class="font-medium text-slate-500">Jumlah order</p>
                        <p class="font-semibold">{{ count($purchase->purchase_orders) }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Jumlah order yang sudah dibayar</p>
                        <p class="font-semibold">
                            {{ $purchase->purchase_orders->where('po_status', 'close')->count() }}
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-500">Jumlah order yang belum dibayar</p>
                        <p class="font-semibold">
                            {{ $purchase->purchase_orders->where('po_status', 'open')->count() }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <x-button href="{{ route('po.allPo', $purchase->id) }}" label="Detail order" primary
                        icon="tag" />
                </div>
            </div>
        </div>
    @empty
        No Data
    @endforelse
    <div class="mt-2">
        {{ $purchases->links() }}
    </div>
</div>
