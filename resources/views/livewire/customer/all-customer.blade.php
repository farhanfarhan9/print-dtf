<div>
    @if (session('customerCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('customerCreated')[0] }}',
                    description: '{{ session('customerCreated')[1] }}',
                    icon: '{{ session('customerCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('customerEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('customerEdited')[0] }}',
                    description: '{{ session('customerEdited')[1] }}',
                    icon: '{{ session('customerEdited')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @endif
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Data Customer
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex sm:justify-between">
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari Customer" />
            <x-button label="Import Customer Data" href="{{ route('customer.upload') }}" class="w-1/3 mt-2 mx-2 sm:w-1/6 sm:mt-0"
                blue icon="upload" />
            <x-button label="Tambah Customer" href="{{ route('customer.create') }}" class="w-1/3 mt-2 sm:w-1/6 sm:mt-0"
                green icon="plus" />
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
                                {{ $customer->province->prov_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $customer->kota->city_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $customer->kecamatans->dis_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $customer->postal }}
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
                                    <x-button wire:click='depositDialog({{ $customer->id }})' label="Deposit" orange
                                        icon="plus" />
                                    <x-button href="{{ route('customer.edit', $customer->id) }}" label="Edit"
                                        primary />
                                    <x-button wire:click="deleteDialog({{ $customer->id }})" label="Hapus" red />
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
            <x-modal.card title="{{ isset($editedUser) ? $editedUser->name : '' }}" blur wire:model.defer="depositModal">
                <x-input type="number" class="!pl-[2.5rem]" label="Jumlah deposit" prefix="Rp."
                    wire:model="machineNo" />

                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <div class="flex">
                            <x-button flat label="Cancel" x-on:click="close" />
                            <x-button primary label="Simpan" wire:click="addDeposit" />
                        </div>
                    </div>
                </x-slot>
            </x-modal.card>
        </div>
        <div class="mt-2">
            {{ $customers->links() }}
        </div>
    </div>
</div>
