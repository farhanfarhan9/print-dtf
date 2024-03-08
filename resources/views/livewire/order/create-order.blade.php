<div>
    <x-slot name="header">
        <a href="{{ route('order.index') }}" wire:navigate
            class="flex items-center w-1/12 gap-1 p-2 mb-5 text-lg rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </x-slot>
    <form method="post" wire:submit="save">
        <div class="flex justify-between mt-10 gap-7">
            <div class="w-2/5">
                <x-card shadow='false'>
                    <x-select label="Cari customer" wire:model.live="customer_id" placeholder="Pilih customer"
                        :async-data="route('api.customers.index')" option-label="name" option-value="id" />

                    <div class="mt-6 space-y-2">
                        @if ($customer_id)
                            <p class="block text-sm font-medium">Alamat: {{ $customer->address }}</p>
                            <p class="block text-sm font-medium">Kota: {{ $customer->city }}</p>
                            <p class="block text-sm font-medium">Kode pos: {{ $customer->postal }}</p>
                            <p class="block text-sm font-medium">Nomor hp: {{ $customer->phone }}</p>
                            <p class="block text-sm font-medium">
                                Total Deposit: Rp. {{ $customer->deposit }}
                                @if ($is_deposit)
                                    <span class="text-green-400">({{ $customer->deposit - $deposit_cut }})</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </x-card>
            </div>
            <div class="w-3/5 space-y-5">
                <x-card shadow='false'>
                    <p class="text-lg font-medium">Orderan</p>
                    <div class="flex justify-between px-8">
                        <div>
                            <p class="text-slate-600">Nama Produk</p>
                            <p class="text-slate-600">{{ $product->nama_produk }}</p>
                        </div>
                        <div>
                            <p class="text-slate-600">Panjang</p>
                            <x-input shadowless="true" wire:model.live='qty' type="number" placeholder="0" />
                        </div>
                        <div>
                            <p class="text-slate-600">Total</p>
                            <p class="text-slate-600">{{ $product_price }}</p>
                        </div>
                    </div>
                    <div class="px-8 mt-3">
                        {{-- <p class="text-slate-600">Ekspedisi</p> --}}
                        <x-select wire:model.live="expedition_id" label='Ekspedisi' placeholder="Pilih ekspedisi"
                            :async-data="route('api.expeditions.index')" option-label="nama_ekspedisi" option-value="id" />
                        <div class="flex justify-end mt-2">

                            @if ($expedition_id)
                                <div class="mt-2">{{ $expedition->ongkir }}</div>
                            @endif
                        </div>
                        @if ($customer_id && $expedition_id)
                            <div class="flex justify-between">
                                <x-checkbox id="right-label" label="Potong deposit" wire:model.live="is_deposit" />
                                @if ($is_deposit)
                                    <p class="text-red-500">{{ $deposit_cut }}</p>
                                @endif
                            </div>
                        @else
                            <div class="flex justify-between">
                                <x-checkbox disabled class="bg-slate-300" id="right-label" label="Potong deposit" />
                            </div>
                        @endif
                    </div>
                    <hr class="my-5">
                    <div class="flex justify-between px-8">
                        <p>Total</p>
                        <p>{{ $total_price }}</p>
                    </div>
                </x-card>
                <x-card shadow='false'>
                    <div class="flex justify-between gap-5">
                        <x-select label="Pilih Status" placeholder="Pilih Status" :options="['Cicil', 'Lunas']"
                            wire:model.live="status" />
                        @if ($status == 'Cicil')
                            <x-input type="number" wire:model='amount' label="Dp" placeholder="Jumlah DP" />
                        @endif
                    </div>
                    @if ($status == 'Cicil')
                        <div class="mt-5">
                            <x-input-label>Testing</x-input-label>
                            <x-input-file wire:model='file'></x-input-file>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />

                        </div>
                    @endif
                </x-card>
                <div class="flex justify-end">
                    <x-button type="submit" spinner label="Simpan" green />
                </div>
            </div>
        </div>
    </form>
</div>
