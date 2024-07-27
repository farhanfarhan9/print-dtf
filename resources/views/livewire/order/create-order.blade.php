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
                        :async-data="route('api.customers.index')" always-fetch option-label="name" option-value="id" />

                    <div class="mt-6 space-y-2">
                        @if ($customer_id)
                            @if ($customer->is_reseller)
                                <p class="mb-5 text-lg font-semibold">Reseller</p>
                            @endif
                            <p class="block text-sm font-medium">Alamat: {{ $customer->address }}</p>
                            <p class="block text-sm font-medium">Kota:
                                {{ optional($customer->kota)->city_name ?? $customer->city_name }}</p>
                            <p class="block text-sm font-medium">Kode pos: {{ $customer->postal }}</p>
                            <p class="block text-sm font-medium">Nomor hp: {{ $customer->phone }}</p>
                            <p class="block text-sm font-medium">
                                Total Deposit: {{ rupiah_format($customer->deposit) }}
                                @if ($is_deposit)
                                    <span class="text-green-400">({{ $customer->deposit - $deposit_cut }})</span>
                                @endif
                            </p>

                        @endif
                    </div>

                    <x-button wire:click='addCustomerModal' label="Tambah User" green />
                    <x-modal wire:model.defer="customerModal" max-width="5xl">
                        <x-card title="Tambah Customer">
                            <div class="space-y-7">
                                <x-input label="Nama Customer" wire:model='name' placeholder="Nama Customer" />
                                <div class="flex justify-between gap-10">

                                    <x-select label="Provinsi" wire:model.live="selectedProvinsi"
                                        placeholder="Select Provinsi" :async-data="route('api.provinsi.index')" option-label="name"
                                        option-value="id" wire:change="updateCities($event.target.value)" />

                                    @if ($selectedProvinsi)
                                        <x-select label="Kota/Kabupaten" wire:model.live="selectedKota"
                                            placeholder="Select Kota" :async-data="route('api.kota.index', ['province' => $selectedProvinsi])" option-label="name"
                                            option-value="id" wire:change="updateDistricts($event.target.value)" />
                                    @endif

                                    @if ($selectedKota)
                                        <x-select label="Kecamatan" wire:model.live="selectedKecamatan"
                                            placeholder="Select Kecamatan" :async-data="route('api.kecamatan.index', ['city' => $selectedKota])" option-label="name"
                                            option-value="id" wire:change="updatePostal($event.target.value)" />
                                    @endif

                                    @if ($selectedKecamatan)
                                        <x-select label="Postal" wire:model.live="selectedPostal"
                                            placeholder="Select Kode Pos" :async-data="route('api.pos.index', [
                                                'province' => $selectedProvinsi,
                                                'city' => $selectedKota,
                                            ])" option-label="name"
                                            option-value="name" />
                                    @endif

                                    {{-- <x-input label="Kota/Kecamatan" wire:model='city' placeholder="Kota/Kecamatan" /> --}}
                                    {{-- <x-input type="number" label="Kode Pos" wire:model='postal' placeholder="Kode Pos" /> --}}
                                </div>
                                <div class="flex justify-between gap-10">
                                    <x-input type="number" label="No. Telp" wire:model='phone'
                                        placeholder="No. Telp" />
                                    <x-inputs.currency type="number" label="Deposit" wire:model='deposit'
                                        placeholder="Deposit" />
                                </div>
                                <x-textarea label="Alamat" wire:model='address' placeholder="Alamat" />
                                <x-checkbox label="Reseller" wire:model="isReseller" />

                            </div>

                            <x-slot name="footer">
                                <div class="flex justify-end gap-x-4">
                                    <x-button flat label="Cancel" x-on:click="close" />
                                    <x-button primary label="Submit" wire:click="addUser" />
                                </div>
                            </x-slot>
                        </x-card>
                    </x-modal>
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
                            <p class="text-slate-600">Panjang (m)</p>
                            @if ($customer_id)
                                <x-input shadowless="true" wire:model.live.debounce.300ms='qty' type="number"
                                    step=".01" placeholder="0" />
                            @else
                                <x-input shadowless="true" disabled type="number" step=".01" placeholder="0" />
                            @endif

                        </div>
                        <div>
                            <p class="text-slate-600">Total</p>
                            @if ($found)
                                <p class="text-slate-600">{{ rupiah_format($product_price) }}</p>
                            @else
                                <p class="text-slate-500">Rp. 0</p>
                            @endif
                        </div>
                    </div>
                    @if ($found == false)
                        <p class="text-red-500">Panjang produk tidak dalam range harga produk</p>
                    @elseif ($qty == 0)
                        <p class="text-red-500">Panjang produk harus lebih besar dari 0</p>
                    @elseif($outOfStock == true)
                        <p class="text-red-500">Stok produk tidak mencukupi</p>
                    @endif
                    <div class="px-8 mt-3">
                        {{-- <p class="text-slate-600">Ekspedisi</p> --}}
                        <x-select wire:model.live="expedition_id" label='Ekspedisi' placeholder="Pilih ekspedisi"
                            :async-data="route('api.expeditions.index')" option-label="nama_ekspedisi" option-value="id" />
                        <div class="mt-2">
                            <x-inputs.currency type="number" label="Biaya Tambahan"
                                wire:model.live.debounce.300ms="additional_price"
                                placeholder="Jumlah biaya tambahan" />
                        </div>
                        <div class="mt-2">
                            <x-inputs.currency type="number" label="Diskon"
                                wire:model.live.debounce.300ms="discount" placeholder="Jumlah diskon" />
                        </div>
                        <div class="flex justify-between mt-2">
                            @if ($expedition_id)
                                <p>Ongkir</p>
                                <div class="mt-2">{{ rupiah_format($expedition->ongkir) }}</div>
                            @endif
                        </div>
                        @if ($additional_price)
                            <div class="flex justify-between mt-2">
                                <p>Biaya Tambahan</p>
                                <div>{{ rupiah_format($additional_price) }}</div>
                            </div>
                        @endif
                        @if ($discount)
                            <div class="flex justify-between mt-2 text-red-500">
                                <p>Diskon</p>
                                <div>{{ rupiah_format($discount) }}</div>
                            </div>
                        @endif
                        @if ($customer_id && $expedition_id)
                            <div class="flex justify-between mt-2">
                                <x-checkbox id="right-label" label="Potong deposit" wire:model.live="is_deposit" />
                                @if ($is_deposit)
                                    <p class="text-red-500">{{ rupiah_format($deposit_cut) }}</p>
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
                        @if ($found)
                            <p>{{ rupiah_format($total_price) }}</p>
                        @else
                            <p class="">Rp. 0</p>
                        @endif
                    </div>
                </x-card>
                <x-card shadow='false'>
                    <div class="flex justify-between gap-5">
                        <x-select label="Pilih Status" placeholder="Pilih Status" :options="['Belum Bayar', 'Cicil', 'Lunas']"
                            wire:model.live="status" />
                        @if ($status == 'Cicil')
                            <x-inputs.currency type="number" wire:model='amount' label="Dp (boleh dikosongkan)"
                                placeholder="Jumlah DP" />
                        @endif
                    </div>
                    @if ($status == 'Cicil')
                        <div class="flex justify-between gap-5 mt-5">
                            <div class="w-1/2">
                                <x-input-label>Bukti pembayaran (boleh dikosongkan)</x-input-label>
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
                    @elseif($status == 'Lunas')
                        <x-select label="Detail bank" placeholder="Detail bank" :options="['BRI', 'BCA', 'BNI', 'CASH']"
                            wire:model.live="bank_detail" />
                    @endif
                </x-card>
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
                    @if ($outOfStock == true || $found == false || $qty == 0)
                        <x-button wire:target="file" label="Simpan" secondary disabled />
                    @else
                        <x-button wire:target="file" wire:loading.remove type="submit" spinner label="Simpan"
                            green />
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
