<div>
    <x-slot name="header">
        <a href="{{ route('customer.index') }}" wire:navigate class="flex items-center w-1/12 gap-1 p-2 mb-5 text-lg rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Data Arsip Customer
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex sm:justify-between">
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari Customer" />
        </div>
        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Provinsi
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Kota/Kabupaten
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Kecamatan
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Kode Pos
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Phone
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Deposit
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Alamat
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $key => $customer)
                        <tr wire:key="{{ $customer->id }}"
                            class="border-b odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $key + 1 }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $customer->name }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($customer->province && $customer->province->prov_name !== null)
                                    {{ $customer->province->prov_name }}
                                @else
                                    {{ $customer->provinsi_name }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($customer->kota && $customer->kota->city_name !== null)
                                    {{ $customer->kota->city_name }}
                                @else
                                    {{ $customer->city_name }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($customer->kecamatans && $customer->kecamatans->dis_name !== null)
                                    {{ $customer->kecamatans->dis_name }}
                                @else
                                    {{ $customer->district_name }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($customer->postal !== null)
                                    {{ $customer->postal }}
                                @else
                                    {{ '' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $customer->phone }}
                            </td>
                            <td class="px-6 py-4">
                                {{ rupiah_format($customer->deposit) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $customer->address }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-5">
                                    <x-button wire:click='restore({{ $customer->id }})' label="Restore" primary />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $customers->links() }}
        </div>
    </div>
</div>
