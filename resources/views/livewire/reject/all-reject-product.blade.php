<div>
    @if (session('dataCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('dataCreated')[0] }}',
                    description: '{{ session('dataCreated')[1] }}',
                    icon: '{{ session('dataCreated')[2] }}',
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
                Reject Product
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 sm:space-x-4">
            <div class="flex space-x-4">
                <x-input wire:model.live="startDate" type="date" placeholder="Tanggal mulai" class="w-full" />
                <x-input wire:model.live="endDate" type="date" placeholder="Tanggal akhir" class="w-full" />
                <x-button wire:click="exportExcel" label="Export" blue icon="download" class="w-full sm:w-auto" />
            </div>
            <x-button label="Tambah Data" href="{{ route('rejected-products.create') }}"
                class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green icon="plus" />
        </div>
        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama Produk
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Stok
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tanggal
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rejecteds as $key => $rejected)
                        <tr wire:key="{{ $rejected->id }}"
                            class="border-b odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $key + 1 }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $rejected->product->nama_produk }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $rejected->stok }}
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($rejected->created_at)->format('d F Y') }}
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
            {{ $rejecteds->links() }}
        </div>
    </div>
</div>
